<template>
  <div class="container d-flex flex-column justify-center align-center">
    <h1>{{$t('Đang làm mới phiên đăng nhập')}}</h1>
  </div>
</template>

<script>
import { isExpiredToken } from "../helpers/token";
export default {
  data() {
    return {};
  },
  created() {
    if (
      !this.$store.state.user.access_token ||
      !this.$store.state.user.refresh_token
    ) {
      this.$store.dispatch("logOut");

      return this.$router.push({
        name: "Login",
        query: { redirect: this.$route.query.redirect },
      });
    }
    if (isExpiredToken(this.$store.state.user.refresh_token)) {
      this.$store.dispatch("logOut");
      return this.$router.push({
        name: "Login",
        query: { redirect: this.$route.query.redirect },
      });
    }
    this.refreshToken(this.$store.state.user.refresh_token);
  },
  methods: {
    refreshToken(refresh_token) {
      const api = new Token();
      api.on("othererror", (api, respone) => {
        console.log("Đã có lỗi xảy ra", respone.error);
      });

      axios
        .post("api/auth/refresh", {
          refreshToken: refresh_token,
        })
        .then((response) => {
          if (response.status >= 400 && response.status < 500) {
            this.$store.dispatch("logOut");
            return this.$router.push({
              name: "Login",
              query: { redirect: this.$route.query.redirect },
            });
          }
          this.$store.dispatch("setAccessToken", result.access_token);
          this.$store.dispatch("setRefreshToken", result.refresh_token);
          return this.$router.push({ path: this.$route.query.redirect });
        })
        .catch((error) => {
          this.$store.dispatch("logOut");
          return this.$router.push({
            name: "Login",
            query: { redirect: this.$route.query.redirect },
          });
        });
    },
  },
};
</script>

<style scoped>
.container {
  background: linear-gradient(to bottom left, #e14eca, #ba54f5, #e14eca);
  width: 100%;
  height: 100%;
}

h1 {
  font-size: 3rem;
  color: rgba(0, 0, 0);
}
</style>