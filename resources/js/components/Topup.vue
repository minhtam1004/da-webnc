<template>
  <section id="accountinfo">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Nạp tiền</h4>
          </mdb-view>
          <mdb-card-body>
            <form v-on:submit.prevent="checkAccountInfo" v-if="!isShowingMoney">
              <label for="accountnumber" class="grey-text">Số tài khoản hoặc tên gợi nhớ</label>
              <input type="text" v-model="accountNumber" class="form-control" />
              <button type="button" class="btn btn-primary px-3">
                <i class="fab fa-android" aria-hidden="true"></i>
              </button>
              <div class="text-center mt-4">
                <button class="btn btn-unique" data-toggle="modal" data-target="#centralModalSuccess">Kiểm tra</button>
                <!-- <button class="btn btn-unique" v-if="isShowingMoney">Xác nhận</button> -->
              </div>
            </form>

            <form v-on:submit.prevent="transfers" v-if="isShowingMoney && !isShowingOPT">
              <label for="accountnumber" class="grey-text">Số tài khoản người nhận</label>
              <input type="text" v-model="accountNumber" readonly class="form-control" />
              <br />

              <label for="accountnumber" class="grey-text">Tên chủ tài khoản</label>
              <input type="text" v-model="name" class="form-control" readonly />
              <br />

              <label for="excess" class="grey-text">Số tiền</label>
              <input type="number" v-model="amount" class="form-control" />

              <br />
              <label for="reason" class="grey-text">Ghi chú</label>
              <textarea class="form-control rounded-0" v-model="reason" rows="3"></textarea>

              <div class="text-center mt-4">
                <button class="btn btn-unique">Hủy bỏ</button>
                <button class="btn btn-indigo">Xác nhận</button>
                <!-- <button class="btn btn-unique" v-if="isShowingMoney">Xác nhận</button> -->
              </div>
            </form>

            <form v-on:submit.prevent="sendOPT" v-if="isShowingOPT">
              <label for="accountnumber" class="grey-text">Nhập mã OPT</label>
              <input type="text" v-model="otpcode" class="form-control" />
              <div>
                <div id="timer" class="text-center">
                  <span id="minute">Thời gian còn lại: {{ minutes }}</span>
                  <span id="colon">:</span>
                  <span id="seconds">{{ seconds }} giây</span>
                </div>
              </div>
              <div class="text-center mt-4">
                <button class="btn btn-unique">Chuyển tiền</button>
                <!-- <button class="btn btn-unique" v-if="isShowingMoney">Xác nhận</button> -->
              </div>
            </form>
              <Modal v-if="showModal" @close-modal="showModal = false" />
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>
<script>
import Modal from "./Modal";
import {
  mdbRow,
  mdbCol,
  mdbCard,
  mdbView,
  mdbCardBody,
  mdbTbl,
  mdbBtn
} from "mdbvue";
export default {
  name: "AccountInfo",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    mdbBtn,
    Modal
  },
  data() {
    return {
      account: this.$store.state.user.authUser.account,
      isShowingMoney: false,
      isShowingOPT: false,
      loading: false,
      accountNumber: 0,
      reason: "",
      amount: 0,
      name: "",
      otpcode: "",
      timer: null,
      totalTime: 10 * 60,
      resetButton: false,
      transferId: null,
      showModal: true
    };
  },
  computed: {
    minutes: function() {
      return this.padTime(Math.floor(this.totalTime / 60));
    },
    seconds: function() {
      return this.padTime(this.totalTime - this.minutes * 60);
    }
  },
  methods: {
    checkAccountInfo() {
      this.loading = true;
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };
      axios
        .get("api/bank/accounts/" + this.accountNumber, {
          headers: {
            Authorization: "bearer" + this.$store.state.user.access_token
          }
        })
        .then(response => {
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.isShowingMoney = true;
            this.name = response.data.name;
          }
        })
        .catch(error => {
          console.log("AXIOS ERROR: ", error);
          if (error.response.status === 404) {
            return this.$toast.open({
              message: "Số tài khoản không đúng! Vui lòng kiểm tra lại",
              type: "error"
            });
          }
          if (error.response.status === 422) {
            return this.$toast.open({
              message: "Bạn không có quyền thực hiện thao tác",
              type: "error"
            });
          }
        });
    },

    transfers() {
      this.loading = true;
      var data = {
        sendId: this.$store.state.user.authUser.account.accountNumber,
        receivedId: this.accountNumber,
        amount: this.amount,
        reason: this.reason
      };
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };
      axios
        .post("api/bank/transfers", data, options)
        .then(response => {
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.$toast.open({
              message: "Khách hàng vui lòng kiểm tra mail và nhập mã OPT",
              type: "success"
            });
            this.transferId = response.data.trasferId;
            this.isShowingOPT = true;
            this.startTimer();
          }
        })
        .catch(error => {
          console.log("AXIOS ERROR: ", error);
          if (error.response.data.error === "Parameter error") {
            return this.$toast.open({
              message: "Dữ liệu không hợp lệ vui lòng kiểm tra lại",
              type: "error"
            });
          }
          if (error.response.data.error === "user exist") {
            return this.$toast.open({
              message: "Tài khoản khách hàng đã tồn tại",
              type: "error"
            });
          }
          console.log(error.response.data);
          console.log(error.response.status);
          console.log(error.response.headers);
        });
    },

    sendOPT() {
      this.loading = true;
      var data = {
        transferId: this.transferId,
        OTPCode: this.otpcode
      };
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };
      axios
        .post("api/confirm/transfers", data, options)
        .then(response => {
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.$toast.open({
              message: "Khách hàng vui lòng kiểm tra mail và nhập mã OPT",
              type: "success"
            });
            this.isShowingOPT = true;
            this.startTimer();
          }
        })
        .catch(error => {
          console.log("AXIOS ERROR: ", error);
          if (error.response.data.error === "Parameter error") {
            return this.$toast.open({
              message: "Dữ liệu không hợp lệ vui lòng kiểm tra lại",
              type: "error"
            });
          }
          if (error.response.data.error === "user exist") {
            return this.$toast.open({
              message: "Tài khoản khách hàng đã tồn tại",
              type: "error"
            });
          }
          console.log(error.response.data);
          console.log(error.response.status);
          console.log(error.response.headers);
        });
    },

    startTimer: function() {
      this.timer = setInterval(() => this.countdown(), 1000);
      this.resetButton = true;
    },
    padTime: function(time) {
      return (time < 10 ? "0" : "") + time;
    },
    countdown: function() {
      this.totalTime--;
    }
  }
};
</script>

<style scoped>
#timer {
  font-size: 4vmin;
  color: blue;
}
#buttons {
  display: flex;
}
button {
  margin: 2px;
}
.profile-card-footer {
  background-color: #f7f7f7 !important;
  padding: 1.25rem;
}
.card.card-cascade .view {
  box-shadow: 0 3px 10px 0 rgba(0, 0, 0, 0.15), 0 3px 12px 0 rgba(0, 0, 0, 0.15);
}
.card.card-cascade .view.gradient-card-header {
  padding: 1rem 1rem;
  text-align: center;
}
.card.card-cascade h3,
.card.card-cascade h4 {
  margin-bottom: 0;
}
</style>
