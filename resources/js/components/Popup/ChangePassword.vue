<template>
  <Popup @close-modal="closeAll" title="Thay đổi mật khẩu" minWidth="130vmin">
    <section id="profile">
      <mdb-row>
        <mdb-col md="12">
          <mdb-card cascade narrow>
            <mdb-card-body style="height:60vh;overflow: auto">
              <form data-toggle="validator" role="form" id="register-form">
                <label for="password" data-error data-success>Mật khẩu hiện tại</label>
                <input
                  type="text"
                  id="password"
                  name="password"
                  v-model="password"
                  class="form-control validate"
                />
                <br />

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
                    type="button"
                    id="btn-one"
                    @click="changePassword"
                  >Xác nhận</button>
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
    Popup
  },
  data() {
    return {
      name: "",
      username: "",
      newpassword: "",
      confirmpassword: "",
      password: "",
      email: "",
      phone: ""
    };
  },
  mounted() {
    $("#password-error").addClass("colorError");
    $("#register-form").validate({
      // validation rules for registration form
      debug: true,
      errorClass: "active",
      rules: {
        password: {
          required: true,
          minlength: 6,
          maxlength: 255
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
        password: {
          required: "Vui lòng nhập mật khẩu",
          minlength: "Mật khẩu từ 6 - 255 kí tự",
          maxlength: "Mật khẩu từ 6 - 255 kí tự"
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
    changePassword() {
      this.turnOnLoading();

      var data = { 
        newpassword: this.newpassword,
        oldpassword: this.password
      };
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };
      axios
        .post("api/auth/changePassword", data, options)
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
    }
  }
};
</script>

<style scoped>
textarea.md-textarea.invalid + label:after {
  width: 100px;
  color: crimson !important;
  top: 115px;
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
