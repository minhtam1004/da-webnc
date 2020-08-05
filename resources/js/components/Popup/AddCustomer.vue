<template>
  <Popup @close-modal="closeAll" title="Thêm mới khách hàng">
    <section id="registercustomer">
      <mdb-row>
        <mdb-col md="12">
          <mdb-card cascade narrow>
            <mdb-card-body style="height:80vh;overflow: auto">
              <form>
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
                  <button
                    class="btn btn-unique"
                    :disabled="loading"
                    id="btn-one"
                    type="button"
                    @click="checkValidateRegisterForm"
                  >
                    <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                    Đăng kí
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
        pleaseTickRecaptchaMessage: "",
      },
    };
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
      this.turnOnLoading();
      this.loading = true;
      var data = {
        username: this.username,
        password: this.password,
        name: this.name,
        email: this.email,
        phone: this.phone,
      };
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      const headers = {
        "Content-Type": "application/json",
        Authorization: "bearer" + this.$store.state.user.access_token,
      };
      axios
        .post("api/auth/register", data, options)
        .then((response) => {
          console.log("RESPONSE RECEIVED: ", response);
          if (response.status == 200) {
            this.$toast.open({
              message: "Thêm mới khách hàng thành công",
              type: "success",
            });
            this.turnOffLoading();
            this.loading = false;
            this.$emit("close-modal");
          }
        })
        .catch((error) => {
          this.turnOffLoading();
          this.loading = false;
          console.log("AXIOS ERROR: ", error);
          if (error.response.data.error === "Parameter error") {
            return this.$toast.open({
              message: "Dữ liệu không hợp lệ vui lòng kiểm tra lại",
              type: "error",
            });
          }
          if (error.response.data.error === "user exist") {
            return this.$toast.open({
              message: "Tài khoản khách hàng đã tồn tại",
              type: "error",
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
            Authorization: "bearer" + this.$store.state.user.access_token,
          },
        })
        .then((response) => {
          console.log(response);
          this.$store.dispatch("setUserObject", response.data);
        })
        .catch((error) => {
          return this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error",
          });
        });
    },
  },
};
</script>

<style scoped>
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
