<template>
  <section id="registercustomer">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Đăng kí tài khoản khách hàng</h4>
          </mdb-view>
          <mdb-card-body>
            <form v-on:submit.prevent="checkValidateRegisterForm">
              <label for="username" class="grey-text" required>Tên đăng nhập</label>
              <input type="text" id="username" v-model="username" class="form-control" />
              <br />
              <label for="password" class="grey-text" required>Mật khẩu</label>
              <input type="password" v-model="password" class="form-control" />
              <br />
              <label for="name" class="grey-text" required>Họ và tên</label>
              <input type="text" id="name" v-model="name" class="form-control" />
              <br />
              <label for="email" class="grey-text" required>Địa chỉ email</label>
              <input type="email" id="email" v-model="email" class="form-control" />
              <br />
              <label for="phone" class="grey-text" required>Số điện thoại</label>
              <input type="text" id="phone" v-model="phone" class="form-control" />
              <div class="text-center mt-4">
                <button class="btn btn-unique">Đăng kí</button>
              </div>
            </form>
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>
<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";
import {
  required,
  minLength,
  maxLength,
  numeric
} from "vuelidate/lib/validators";
export default {
  name: "RegisterCustomer",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl
  },
  data() {
    return {
      username: "",
      password: "",
      email: "",
      name: "",
      phone: "",
      message: "",
      show: false,
      loading: false,
      msg: [],
      loginForm: {
        recaptchaVerified: false,
        pleaseTickRecaptchaMessage: ""
      }
    };
  },
  methods: {
    checkValidateRegisterForm() {
      this.register();
    },
    reset() {
      (this.username = ""),
        (this.password = ""),
        (this.name = ""),
        (this.email = ""),
        (this.phone = "");
    },
    register() {
      this.loading = true;
      var data = {
        username: this.username,
        password: this.password,
        name: this.name,
        email: this.email,
        phone: this.phone
      };
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };
      const headers = {
        "Content-Type": "application/json",
        Authorization: "bearer" + this.$store.state.user.access_token
      };
      axios
        .post("api/auth/register", data, options)
        .then(response => {
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.$toast.open({
              message: "Thêm mới khách hàng thành công",
              type: "success"
            });
          }
        })
        .catch(error => {
          console.log("AXIOS ERROR: ", error);
          if (error.response.data.error === "Parameter error") {
            return this.$toast.open({
              message: "Dữ liệu không hợp lệ vui lòng kiểm tra lại",
              type: "error"
            });
          }
          if (error.response.data.error === "user exist") {
            return this.$toast.open({
              message: "Tài khoản khách hàng đã tồn tại",
              type: "error"
            });
          }
          console.log(error.response.data);
          console.log(error.response.status);
          console.log(error.response.headers);
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
