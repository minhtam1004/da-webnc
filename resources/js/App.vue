<template>
  <v-app>
    <template>
      <router-view />
    </template>
  </v-app>
</template>
<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer>
</script> 
<script>
export default {
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
      function(error) {
        this.$store.dispatch("logOut")
        // Do something with response error
        return Promise.reject(error);
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