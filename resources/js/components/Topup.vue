<template>
  <section id="accountinfo">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Chuyển khoản nội bộ</h4>
          </mdb-view>
          <mdb-card-body>
            <form v-if="!isShowingMoney">
              <label for="accountnumber" class="grey-text">Số tài khoản hoặc tên gợi nhớ</label>
              <div class="form-row mb-4">
                <div class="col-10">
                  <input type="text" v-model="accountNumber" class="form-control" />
                </div>
                <div class="col">
                  <button
                    type="button"
                    class="btn-floating purple-gradient px-3"
                    style="height:100%;border-radius: 0.5vmin"
                    @click="showSearchName = true"
                  >
                    <i class="fas fa-address-book" aria-hidden="true"></i>
                  </button>
                </div>
              </div>

              <div class="text-center mt-4">
                <button
                  class="btn btn-unique"
                  data-toggle="modal"
                  data-target="#centralModalSuccess"
                  type="button"
                  @click="checkAccountInfo"
                >Kiểm tra</button>
              </div>
              <SearchName v-if="showSearchName" @close-modal="showSearchName = false" />
            </form>

            <form v-if="isShowingMoney && !isShowingOPT">
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

              <br />
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="defaultUnchecked" />
                <label class="custom-control-label" for="defaultUnchecked">Người nhận trả phí</label>
              </div>

              <br />
              <div style="display: flex;justify-content: flex-end;">
              <div class="card" style="width: 50vmin;">
                <div class="card-body" style="">
                  <p class="card-text blue-text">
                    - Số tiền tối thiếu chuyển khoản: 10.000 VNĐ
                  </p>
                  <p class="card-text blue-text">
                    - Số tiền tối đa chuyển khoản: 1 tỷ VNĐ
                  </p>
                  <p class="card-text blue-text">
                    - Phí giao dịch chuyển tiền nội bộ: 3.000 VNĐ
                  </p>
                  
                </div>
              </div>
              </div>
              <div class="text-center mt-4">
                <button class="btn btn-unique" type="button" @click="backTo">Quay lại</button>
                <button
                  type="button"
                  id="btn-one"
                  class="btn btn-primary"
                  @click="transfers"
                >Xác nhận</button>
              </div>
            </form>

            <form v-if="isShowingOPT">
              <label for="accountnumber" class="grey-text">Nhập mã OPT</label>
              <input
                type="text"
                v-model="otpcode"
                :disabled="minutes == 0 && seconds == 0"
                class="form-control"
              />
              <div v-if="parseInt(minutes) == 0 && parseInt(seconds) == 0">
                <div id="timer" class="text-center">
                  <span id="minute">Thời gian còn lại: {{ minutes }}</span>
                  <span id="colon">:</span>
                  <span id="seconds">{{ seconds }} giây</span>
                </div>
              </div>
              <div class="text-center mt-4">
                <button
                  class="btn btn-warning"
                  v-if="minutes == 0 && seconds == 0"
                  type="button"
                >Lấy lại mã OPT</button>
                <button
                  class="btn btn-unique"
                  @click="sendOPT"
                  type="button"
                  v-if="!showAddList"
                >Chuyển tiền</button>
                <button
                  class="btn btn-default"
                  v-if="showAddList"
                  type="button"
                  @click="addToListReminder"
                >
                  <i class="fab fa-mdb"></i>Thêm vào gợi nhớ
                </button>
                <button
                  class="btn btn-warning"
                  v-if="showAddList"
                  type="button"
                  @click="$router.push({ name: 'Dashboard'})"
                >Thực hiện giao dịch khác</button>
              </div>
            </form>
            <Modal
              v-if="showModal"
              :type="typeModal"
              :title="titleModal"
              :message="messageModal"
              @close-modal="showModal = false"
            />
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>
<script>
import Modal from "./Modal";
import SearchName from "./SearchName";
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
    Modal,
    SearchName
  },
  data() {
    return {
      account: this.$store.state.user.authUser.account,
      isShowingMoney: false,
      isShowingOPT: false,
      accountNumber: this.$store.state.transfer.accountNumber,
      reason: "",
      amount: 0,
      name: "",
      otpcode: "",
      timer: null,
      totalTime: 10,
      resetButton: false,
      transferId: null,
      showModal: false,
      typeModal: "",
      titleModal: "",
      messageModal: "",
      disabled: false,
      loading: false,
      showSearchName: false,
      showAddList: false
    };
  },
  computed: {
    minutes: function() {
      return this.padTime(Math.floor(this.totalTime / 60));
    },
    seconds: function() {
      return this.padTime(this.totalTime - this.minutes * 60);
    },
    accountNum: function() {
      return this.$store.state.transfer.accountNumber;
    }
  },
  methods: {
    backTo() {
      console.log("Vo");
      this.isShowingMoney = false;
    },
    checkAccountInfo() {
      this.turnOnLoading();
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };
      axios
        .get(
          "api/bank/accounts/" +
            (this.accountNum > 0 ? this.accountNum : this.accountNumber),
          {
            headers: {
              Authorization: "bearer" + this.$store.state.user.access_token
            }
          }
        )
        .then(response => {
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.isShowingMoney = true;
            this.name = response.data.name;
            this.showModal = true;
            this.typeModal = "success";
            this.messageModal = "Số tài khoản hợp lệ";
            this.titleModal = "Thao tác thành công";
            this.turnOffLoading();
          }
        })
        .catch(error => {
          this.turnOffLoading();
          this.loading = false;
          this.disabled = false;
          console.log("AXIOS ERROR: ", error);
          this.showModal = true;
          this.typeModal = "danger";
          this.titleModal = "Thao tác thất bại";
          if (error.response.status === 404) {
            this.messageModal =
              "Số tài khoản không hợp lệ! Vui lòng kiểm tra lại";

            return;
          }
          if (error.response.status === 422) {
            this.$store.dispatch("logOut")
            this.messageModal = "Bạn không có quyền thực hiện thao tác";
            return;
          }
        });
      this.$emit("check-info");
    },

    turnOnLoading() {
      $("#btn-one")
        .html(
          '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Xác nhận'
        )
        .addClass("disabled");
    },
    turnOffLoading() {
      $("#btn-one").removeClass("disabled");
      $("#btn-one span").remove();
    },
    transfers() {
      this.turnOnLoading();
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
            this.turnOffLoading();
            this.showModal = true;
            this.typeModal = "success";
            this.messageModal =
              "Khách hàng vui lòng kiểm tra mail và nhập mã OPT";
            this.titleModal = "Thao tác thành công";
            this.transferId = response.data.transferId;
            this.isShowingOPT = true;
            this.startTimer();
          }
        })
        .catch(error => {
          console.log("AXIOS ERROR: ", error);
          this.turnOffLoading();
          this.showModal = true;
          this.typeModal = "danger";
          this.titleModal = "Thao tác thất bại";
          if (error.response.data.error === "Parameter error") {
            this.messageModal = "Dữ liệu không hợp lệ vui lòng kiểm tra lại";
            return;
          }
          if (error.response.data.error === "user exist") {
            this.messageModal = "Tài khoản khách hàng đã tồn tại";
            return;
          }
        });
    },

    sendOPT() {
      this.turnOnLoading();
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
          if (response.data) {
            if (response.data === "success") {
              this.showModal = true;
              this.typeModal = "success";
              this.messageModal = "Khách hàng đã chuyển tiền thành công";
              this.titleModal = "Thao tác thành công";
            }
            this.turnOffLoading();
            // this.isShowingOPT = false;
            // this.isShowingMoney = false;
            this.showAddList = true;
            // this.$router.push({ name: "Topup" });
          }
        })
        .catch(error => {
          this.turnOffLoading();
          this.showModal = true;
          this.typeModal = "danger";
          this.titleModal = "Thao tác thất bại";
          console.log("AXIOS ERROR: ", error);
          if (error.response.data.error === "Parameter error") {
            this.messageModal = "Dữ liệu không hợp lệ vui lòng kiểm tra lại";
            return;
          }
          if (error.response.data.error === "confirm error") {
            this.messageModal = "Mã OTP không đúng vui lòng kiểm tra lại";
            return;
          }
        });
    },

    addToListReminder() {},

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
button:focus {
  outline: 0;
}
#timer {
  font-size: 2.5vmin;
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
