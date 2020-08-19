<template>
  <v-app>
    <template>
      <router-view />
      <Modal
        v-if="showModal"
        :type="typeModal"
        :title="titleModal"
        :message="messageModal"
        @close-modal="showModal = false"
      />
    </template>
  </v-app>
</template>
<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer>
</script> 
<script>
import Modal from "./components/Modal";
export default {
  components: {
    Modal,
  },
  data() {
    return {
      showModal: false,
      typeModal: "",
      titleModal: "",
      messageModal: "",
    };
  },
  computed: {
    roleId() {
      console.log(this.$store.state.user.authUser);
      return this.$store.state.user.authUser
        ? this.$store.state.user.authUser.roleId
        : null;
    },
    currentRoute() {
      console.log("Do");
      console.log(this.$route);
      return this.$route;
    },
  },
  // watch: {
  //   currentRoute() {
  //     console.log("Vo");
  //     this.redirectPage();
  //   }
  // },
  mounted() {
    console.log("daa");
    this.redirectPage();
  },
  created() {
    this.reloadUser();
    axios.interceptors.response.use(
      function (response) {
        // Do something with response data
        return response;
      },
      (error) => {
        console.log("app", error.response.data);
        if (error.response.data === "unauthorization") {
          axios
            .post(
              "api/auth/refresh",
              { refreshToken: this.$store.state.user.refresh_token},
              {
                headers: {
                  "Content-Type": "application/json",
                  Authorization: "bearer" + this.$store.state.user.access_token,
                },
              }
            )
            .then((response) => {
              this.$store.dispatch(
                "setAccessToken",
                response.data.access_token
              );
              this.$store.dispatch(
                "setRefreshToken",
                response.data.refresh_token
              );
            })
            .catch((error) => {
              this.$store.dispatch("logOut");
              localStorage.removeItem(this.$store.state.user.authUser);
              localStorage.removeItem(this.$store.state.user.access_token);
              this.$router.push({ name: "Login" });
              return;
            });
          // this.$store.dispatch("logOut");
          // localStorage.removeItem(this.$store.state.user.authUser);
          // localStorage.removeItem(this.$store.state.user.access_token);
          // console.log("A", this.$router);
          // this.$router.push({ name: "Login" });
          // return;
        }
        this.showModal = true;
        this.typeModal = "danger";
        this.messageModal = "Có lỗi xảy ra";
        this.titleModal = "Thao tác thất bại";
        // return Promise.reject(error);
      }
    );
  },
  methods: {
    redirectPage() {
      console.log("Vo 1");
      if (!this.$route.meta.role) {
        console.log("thoat");
        return;
      }
      console.log(this.roleId);
      console.log(this.$route.meta.role);
      if (
        !this.roleId ||
        (this.roleId && this.roleId > this.$route.meta.role)
      ) {
        console.log("Vo 2");
        console.log("B", this.$router);
        this.$router.push({ name: "Login" });
      }
    },
    reloadUser() {
      if (this.accessToken && this.$store.state.user.refresh_token) {
        if (isExpiredToken(this.accessToken)) {
          return this.$router.push({
            name: "RefreshToken",
            query: { redirect: this.$route.fullPath },
          });
        }
        return this.loadUser(this.accessToken);
      }
    },
    loadUser(accessToken) {
      const { pathname, search } = { ...window.location };

      axios
        .get("api/auth/me", {
          headers: {
            Authorization: "bearer" + accessToken,
          },
        })
        .then((response) => {
          this.$store.dispatch("setUserObject", result);
        })
        .catch((error) => {
          return this.$router.push({
            name: "RefreshToken",
            query: { redirect: pathname + search },
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
</style>