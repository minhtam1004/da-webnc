<template>
  <Popup @close-modal="closeAll" title="Lấy lại mật khẩu" minWidth="130vmin">
    <section id="profile">
      <mdb-row>
        <mdb-col md="12">
          <mdb-card cascade narrow>
            <mdb-card-body style="height:60vh;overflow: auto">
              <form data-toggle="validator" role="form" id="register-form" v-if="isSendEmail">
                <label for="email" data-error data-success>Nhập địa chỉ email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  v-model="email"
                  class="form-control validate"
                />
                <br />

                <div class="text-center mt-4">
                  <button
                    class="btn btn-unique"
                    type="button"
                    :disabled="loading"
                    id="btn-one"
                    @click="sendEmail"
                  >
                    <i v-if="loading" class="fa fa-spinner fa-spin"></i>Xác nhận
                  </button>
                </div>
              </form>
              <form data-toggle="validator" role="form" id="register-form" v-else>
                <label for="email" data-error data-success>Địa chỉ email</label>
                <input type="email" v-model="emailRight" readonly class="form-control validate" />
                <br />
                <label for="accountnumber" class="grey-text">Nhập mã OTP</label>
                <input
                  type="text"
                  v-model="otpcode"
                  :disabled="minutes == 0 && seconds == 0"
                  class="form-control"
                />
                <div v-if="parseInt(minutes) >= 0 || parseInt(seconds) > 0">
                  <div id="timer" class="text-center">
                    <span id="minute">Thời gian còn lại: {{ minutes }}</span>
                    <span id="colon">:</span>
                    <span id="seconds">{{ seconds }} giây</span>
                  </div>
                </div>

                <label for="newpassword" data-error data-success>Mật khẩu mới</label>
                <input
                  type="text"
                  id="newpassword"
                  name="newpassword"
                  v-model="newpassword"
                  class="form-control validate"
                />
                <br />

                <label for="confirmpassword" data-error data-success>Xác nhận mật khẩu</label>
                <input
                  type="text"
                  id="confirmpassword"
                  name="confirmpassword"
                  v-model="confirmpassword"
                  class="form-control validate"
                />

                <div class="text-center mt-4">
                  <button
                    class="btn btn-unique"
                    @click="resetPassword"
                    type="button"
                    id="btn-one"
                  >Xác nhận</button>
                </div>
                <!-- <label for="email" data-error data-success>Nhập địa chỉ email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  v-model="email"
                  class="form-control validate"
                />
                <br />

                <div class="text-center mt-4">
                  <button
                    class="btn btn-unique"
                    type="button"
                    id="btn-one"
                    @click="sendEmail"
                  >Xác nhận</button>
                </div>-->
                <Modal
                  v-if="showModal"
                  :type="typeModal"
                  :title="titleModal"
                  :message="messageModal"
                  @close-modal="showModal = false"
                />
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
    Modal
  },
  data() {
    return {
      name: "",
      username: "",
      newpassword: "",
      confirmpassword: "",
      password: "",
      email: "",
      phone: "",
      isSendEmail: true,
      totalTime: 60,
      timer: null,
      showModal: false,
      typeModal: "",
      titleModal: "",
      messageModal: "",
      otpcode: "",
      emailRight: "",
      loading: false
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
  mounted() {
    $("#password-error").addClass("colorError");
    $("#register-form").validate({
      // validation rules for registration form
      debug: true,
      errorClass: "active",
      rules: {
        email: {
          required: true,
          email: true
        },
        newpassword: {
          required: true,
          minlength: 6,
          maxlength: 255
        },
        confirmpassword: {
          required: true,
          minlength: 5,
          maxlength: 255,
          equalTo: "#newpassword"
        }
      },
      messages: {
        email: {
          required: "Vui lòng nhập địa chỉ email",
          email: "Địa chỉ email không hợp lệ"
        },
        newpassword: {
          required: "Vui lòng nhập mật khẩu",
          minlength: "Mật khẩu từ 6 - 255 kí tự",
          maxlength: "Mật khẩu từ 6 - 255 kí tự"
        },
        confirmpassword: {
          required: "Vui lòng nhập mật khẩu",
          minlength: "Mật khẩu từ 6 - 255 kí tự",
          maxlength: "Mật khẩu từ 6 - 255 kí tự",
          equalTo: "Mật khẩu không khớp"
        }
      },
      submitHandler: function(form) {
        form.submit();
      }
    });
  },
  methods: {
    closeAll() {
      this.$emit("close-modal");
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
    sendEmail() {
      this.turnOnLoading();
      this.loading = true;
      axios
        .get("api/auth/resetPassword?email=" + this.email, {})
        .then(response => {
          this.showModal = true;
          this.turnOffLoading();
          this.loading = false;
          if (!response) {
            this.typeModal = "danger";
            this.messageModal = "Tài khoản không tồn tại";
            this.titleModal = "Thao tác thất bại";
            return;
          }

          this.emailRight = response.data.email;
          this.typeModal = "success";
          this.messageModal = "Địa chỉ email hợp lệ";
          this.titleModal = "Thao tác thành công";
          this.isSendEmail = false;
          this.startTimer();
        })
        .catch(error => {
          this.turnOffLoading();
          this.loading = false;
          this.showModal = true;
          this.typeModal = "danger";
          this.messageModal = "Tài khoản không tồn tại";
          this.titleModal = "Thao tác thất bại";
        });
    },
    resetPassword() {
      this.turnOnLoading();
      var data = {
        token: this.otpcode,
        email: this.emailRight,
        password: this.newpassword
      };
      axios
        .post("api/auth/resetPassword", data, {})
        .then(response => {
          this.turnOffLoading();
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.$toast.open({
              message: "Thay đổi mật khẩu thành công",
              type: "success"
            });
            this.$emit("close-modal");
          }
        })
        .catch(error => {
          this.turnOffLoading();
          console.log("AXIOS ERROR: ", error);
          if (error.response.data.error === "password invalid") {
            return this.$toast.open({
              message: "Mật khẩu hiện tại không đúng",
              type: "error"
            });
          }
          // if (error.response.data.error === "user exist") {
          //   return this.$toast.open({
          //     message: "Tài khoản khách hàng đã tồn tại",
          //     type: "error"
          //   });
          // }
          console.log(error.response);
          console.log(error.response.status);
          console.log(error.response.data.error);
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
