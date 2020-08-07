<template>
  <Popup @close-modal="closeAll" title="Xác nhận thanh toán nhắc nợ" minWidth="130vmin">
    <section id="profile">
      <mdb-row>
        <mdb-col md="12">
          <mdb-card cascade narrow>
            <mdb-card-body style="height:60vh;overflow: auto">
              <form data-toggle="validator" role="form" id="register-form">
                <label for="otpcode" data-error data-success>Xác nhận mã OTP</label>
                <input
                  type="text"
                  id="otpcode"
                  name="otpcode"
                  v-model="otpcode"
                  class="form-control validate"
                />

                <div id="timer" class="text-center">
                  <span id="minute">Thời gian còn lại: {{ minutes }}</span>
                  <span id="colon">:</span>
                  <span id="seconds">{{ seconds }} giây</span>
                </div>
                <br />

                <div class="text-center mt-4">
                  <button
                    class="btn btn-primary"
                    type="button"
                    :disabled="loading"
                    id="btn-payment"
                    @click="paymentDebt"
                  >
                    <i v-if="loading" class="fa fa-spinner fa-spin"></i>Xác nhận
                  </button>
                </div>
              </form>
            </mdb-card-body>
          </mdb-card>
        </mdb-col>
      </mdb-row>
    </section>
  </Popup>
</template>
<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";
import Modal from "./../Modal";
import $ from "jquery";
import validate from "jquery-validation";
import Popup from "./Popup";
export default {
  name: "Profile",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    Popup,
    Modal,
  },
  props: {
    idPayment: {
      tyle: String,
      default: "",
    },
  },
  data() {
    return {
      id: this.$route.params.id,
      loading: false,
      otpcode: "",
      totalTime: 60,
    };
  },
  computed: {
    minutes: function () {
      return this.padTime(Math.floor(this.totalTime / 60));
    },
    seconds: function () {
      return this.padTime(this.totalTime - this.minutes * 60);
    },
    accountNum: function () {
      return this.$store.state.transfer.accountNumber;
    },
  },
  watch: {
    seconds() {
      if (this.seconds == 0) {
        this.showTime = false;
      }
    },
  },
  created() {
    this.startTimer();
  },
  methods: {
    startTimer: function () {
      this.timer = setInterval(() => this.countdown(), 1000);
      this.resetButton = true;
    },
    padTime: function (time) {
      return (time < 10 ? "0" : "") + time;
    },
    countdown: function () {
      this.totalTime--;
    },
    closeAll() {
      this.$emit("close-modal");
    },
    turnOnLoading() {
      $("#btn-payment")
        .html(
          '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Xác nhận'
        )
        .addClass("disabled");
    },
    turnOffLoading() {
      $("#btn-payment").removeClass("disabled");
      $("#btn-payment span").remove();
    },
    paymentDebt() {
      this.turnOnLoading();
      this.loading = true;
      var data = {
        OTPCode: this.otpcode,
      };
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };

      axios
        .post(
          "api/bank/transfers/" + this.idPayment + "/confirm",
          data,
          options
        )
        .then((response) => {
          if (response.status == 200) {
          this.turnOffLoading();
          this.loading = false;
          this.$toast.open({
            message: "Thanh toán nhắc nợ thành công",
            type: "success",
          });
          this.$emit("close-modal");
          this.$router.push({ name: 'DebtReminder'});
          }
        })
        .catch((error) => {
          this.turnOffLoading();
          this.loading = false;
          this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "danger",
          });
        });
    },
  },
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
