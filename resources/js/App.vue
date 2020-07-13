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
    Modal
  },
  data() {
    return {
      showModal: false,
      typeModal: "",
      titleModal: "",
      messageModal: ""
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
    }
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
    axios.interceptors.response.use(
      function(response) {
        // Do something with response data
        return response;
      },
      error => {
        console.log("app", error.response.data);
        if (error.response.data === "unauthorization") {
          this.$store.dispatch("logOut");
          localStorage.removeItem(this.$store.state.user.authUser);
          localStorage.removeItem(this.$store.state.user.access_token);
          this.$router.push({ name: "Login" });
          return;
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
        this.$router.push({ name: "Login" });
      }
    }
  }
};
</script>
<style scoped>
button:focus {
  outline: 0;
}
</style>