<template>
  <!-- Default form login -->
  <div id="app" class="container-login">
    <form role="form">
      <p class="h4 text-center mb-4">Đăng nhập</p>

      <div class="form-group has-error">
        <label for="name" class="grey-text control-label">Tên đăng nhập</label>
        <input type="text" v-model="username" required class="form-control" />
        <span class="help-block">{{msg.username}}</span>
        <div class="help-block">
          <strong>{{ loginForm.pleaseTickRecaptchaMessage }}</strong>
        </div>
      </div>
      <br />
      <label for="password" class="grey-text" required>Mật khẩu</label>
      <input type="password" v-model="password" class="form-control" />
      <br />
      <div class="mb-3" style="display: flex;justify-content: flex-end;">
      <a @click="showResetPass = true" style="color: #33B5E5;text-decoration: underline;">Quên mật khẩu?</a>
      </div>
      <vue-recaptcha
        @verify="markRecaptchaAsVerified"
        sitekey="6Lcz-6IZAAAAADIWCpKp2llNX1nfToLClom240Y7"
        :loadRecaptchaScript="true"
      ></vue-recaptcha>
      <div class="text-center mt-4">
        <button
          class="btn btn-indigo"
          :disabled="loading"
          id="btn-one"
          type="button"
          @click="checkIfRecaptchaVerified"
        >
          <i v-if="loading" class="fa fa-spinner fa-spin"></i>
          Đăng nhập
        </button>
      </div>
    </form>
    <Modal
      v-if="showModal"
      :type="typeModal"
      :title="titleModal"
      :message="messageModal"
      @close-modal="showModal = false"
    />
    <ResetPassword v-if="showResetPass" @close-modal="showResetPass=false"  />
  </div>
  <!-- Default form login -->
</template>
<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer>
</script>
<script>
import Modal from "./Modal";
import VueRecaptcha from "vue-recaptcha";
import ResetPassword from "./Popup/ResetPassword";
import {
  required,
  minLength,
  maxLength,
  numeric
} from "vuelidate/lib/validators";
export default {
  components: { VueRecaptcha, Modal, ResetPassword },
  // el: "#app",
  data() {
    return {
      username: "",
      password: "",
      showModal: false,
      typeModal: "",
      titleModal: "",
      messageModal: "",
      showResetPass: false,
      show: false,
      loading: false,
      msg: [],
      loginForm: {
        recaptchaVerified: false,
        pleaseTickRecaptchaMessage: ""
      }
    };
  },
  computed: {
    validateName() {
      if (this.$v.username.$dirty) {
        if (!this.$v.username.required) {
          return `Tên đăng nhập không được để trống`;
        }
        if (!this.$v.username.minLength) {
          return `Tên đăng nhập phải từ ${this.$v.username.$params.minLength.min} đến ${this.$v.username.$params.maxLength.max} kí tự`;
        }
        if (!this.$v.username.maxLength) {
          return `Tên đăng nhập phải từ ${this.$v.username.$params.minLength.min} đến ${this.$v.username.$params.maxLength.max} kí tự`;
        }
        if (this.$v.error_code === 422) {
          return `Tên đăng nhập hoặc mật khẩu không đúng`;
        }

        if (!this.$v.username.isCorrectFormat) {
          return `Tên đăng nhập không đúng định dạng`;
        }
      }
      return "";
    }
  },
  validations() {
    const validate = {
      username: {
        required,
        minLength: minLength(6),
        maxLength: maxLength(32),
        isCorrectFormat: function() {
          var specialChars = "<>@!#$%^&*()+[]{}?:;|'\"\\,/~`-=";
          for (let i = 0; i < specialChars.length; i++) {
            if (this.username.indexOf(specialChars[i]) !== -1) {
              return false;
            }
          }
          return true;
        }
      }
    };

    if (this.required2fa) {
      validate.f2a_code = {
        required,
        numeric,
        length: value => typeof value === "string" && value.length === 6
      };
    }
    return validate;
  },
  watch: {
    username(value) {
      // binding this to the data value in the email input
      this.username = value;
      this.validateUsername(value);
    }
  },
  methods: {
    validateUsername(value) {
      if (value.length > 0) {
        this.msg["username"] = "";
      } else {
        this.msg["username"] = "Username không được bỏ trống";
      }
    },
    markRecaptchaAsVerified(response) {
      this.loginForm.pleaseTickRecaptchaMessage = "";
      this.loginForm.recaptchaVerified = true;
    },
    checkIfRecaptchaVerified() {
      // if (!this.loginForm.recaptchaVerified) {
      //   this.showModal = true;

      //   this.typeModal = "danger";
      //   this.messageModal = "Vui lòng nhập captcha";
      //   this.titleModal = "Thao tác không thành công";
      //   return true;
      // }
      this.login();
    },
    login() {
      this.turnOnLoading();
      console.log(this.username, this.password);
      this.loading = true;
      axios
        .post("api/auth/login", {
          username: this.username,
          password: this.password
        })
        .then(response => {
          console.log(response);
          this.$store.dispatch("setAccessToken", response.data.access_token);
          this.loading = false;
          this.getUser();
          this.$toast.open({
            message: "Đăng nhập thành công",
            type: "success"
          });
        })
        .catch(error => {
          if (error.response.status === 401) {
            this.turnOffLoading();
            this.loading = false;
            return this.$toast.open({
              message: "Tên đăng nhập hoặc mật khẩu không đúng",
              type: "error"
            });
          }
        });
    },
    getUser() {
      axios
        .get("api/auth/me", {
          headers: {
            Authorization: "bearer" + this.$store.state.user.access_token
          }
        })
        .then(response => {
          this.turnOffLoading();
          console.log("=====", response);
          this.$store.dispatch("setUserObject", response.data);
          this.$user = response.data;
          this.$router.push({ name: "Dashboard" });
        })
        .catch(error => {
          this.turnOffLoading();
          return this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error"
          });
        });
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
    }
  }
};
</script>
<style scoped>
.container-login {
  width: 50%;
  margin: auto;
}

.placement {
  position: absolute;
  right: 0;
}
</style>