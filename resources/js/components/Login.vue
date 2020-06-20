<template>
  <!-- Default form login -->
  <div class="container-login">
    <p class="h4 text-center mb-4">Sign in</p>
    <label for="name" class="grey-text">Username</label>
    <input type="text" v-model="username" class="form-control" />
    <br />
    <label for="password" class="grey-text">Password</label>
    <input type="password" v-model="password" class="form-control" />

    <br />
    <vue-recaptcha sitekey="6Lcz-6IZAAAAADIWCpKp2llNX1nfToLClom240Y7" :loadRecaptchaScript="true"></vue-recaptcha>

    <div class="text-center mt-4">
      <button class="btn btn-indigo" :disabled="loading" @click="login()">
        <i v-if="loading" class="fa fa-spinner fa-spin"></i>
        Login
      </button>
    </div>
  </div>
  <!-- Default form login -->
</template>
<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer>
</script>
<script>
import VueRecaptcha from "vue-recaptcha";
export default {
  components: { VueRecaptcha },
  data() {
    return {
      username: "",
      password: "",
      show: false,
      loading: false
    };
  },
  methods: {
    login() {
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
          this.$router.push({ name: "Dashboard" });
        })
        .catch(error => {
          if (error.response.status === 401) {
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
          console.log(response);
          this.$store.dispatch("setUserObject", response.data);
        })
        .catch(error => {
          return this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error"
          });
        });
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