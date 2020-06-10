<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="7" md="7">
        <v-card class="elevation-12">
          <v-toolbar
            style="justify-content: center!important"
            color="primary"
            dark
            src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
            prominent
            flat
          >
            <v-toolbar-title>Đăng nhập</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form ref="form" v-model="valid" lazy-validation>
              <v-text-field label="Tên đăng nhập" append-icon="mdi-account" type="text" outlined></v-text-field>

              <v-text-field
                v-model="password"
                :append-icon="
                                    isShowingPassword
                                        ? 'mdi-eye'
                                        : 'mdi-eye-off'
                                "
                :rules="[rules.required, rules.min]"
                :type="isShowingPassword ? 'text' : 'password'"
                outlined
                label="Mật khẩu"
                @click:append="
                                    isShowingPassword = !isShowingPassword
                                "
              ></v-text-field>

              <vue-recaptcha
                sitekey="6Lcz-6IZAAAAADIWCpKp2llNX1nfToLClom240Y7"
                :loadRecaptchaScript="true"
              ></vue-recaptcha>
            </v-form>
          </v-card-text>
          <v-card-actions style="justify-content: center;">
            <v-row>
              <v-col md="6" sm="6" cols="12" class="text-right">
                <v-btn color="primary" outlined @click="$router.push({ name: 'Register' })">Đăng kí</v-btn>
              </v-col>
              <v-col md="6" sm="6" cols="12">
                <v-btn color="primary" @click="login()">Đăng nhập</v-btn>
              </v-col>
            </v-row>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer>
</script>

<script>
import VueRecaptcha from "vue-recaptcha";
export default {
  components: { VueRecaptcha },
  data() {
    return {
      valid: true,
      isShowingPassword: false,
      sitekey: process.env.RECAPTCHA_SITE_KEY,
      password: "",
      rules: {
        required: value => !!value || "Mật khẩu không được bỏ trống",
        min: v =>
          (v.length < 6 && v.length > 32) || "Mật khẩu từ 6 đến 32 kí tự"
      }
    };
  },
  methods: {
    login() {},
  }
};
</script>
