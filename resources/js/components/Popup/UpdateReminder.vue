<template>
  <Popup @close-modal="closeAll" title="Thay đổi tên gợi nhớ" minWidth="130vmin">
    <section id="profile">
      <mdb-row>
        <mdb-col md="12">
          <mdb-card cascade narrow>
            <mdb-card-body style="height:60vh;overflow: auto">
              <form data-toggle="validator" role="form" id="register-form">
                <label for="newName" data-error data-success>Nhập tên gợi nhớ</label>
                <input
                  type="text"
                  id="newName"
                  name="newName"
                  v-model="newName"
                  class="form-control validate"
                />
                <br />

                <div class="text-center mt-4">
                  <button
                    class="btn btn-unique"
                    type="button"
                    id="btn-one"
                    @click="addToListReminder"
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
    Popup,
  },
  props: {
    accountId: {
      type: String,
      default: "",
    },
    bankId: {
      type: String,
      default: "",
    },
    name: {
      type: String,
      default: "",
    },
    id: {
      type: Number,
      default: 0,
    }
  },
  data() {
    return {
      newName: ""
    };
  },
  created() {
    this.newName = this.name;
  },
  mounted() {
    $("#password-error").addClass("colorError");
    $("#register-form").validate({
      // validation rules for registration form
      debug: true,
      errorClass: "active",
      rules: {
        name: {
          required: true,
          minlength: 1,
          maxlength: 32,
        },
      },
      messages: {
        name: {
          required: "Vui lòng nhập tên gợi nhớ",
          minlength: "Mật khẩu từ 2 - 32 kí tự",
          maxlength: "Mật khẩu từ 2 - 32 kí tự",
        },
      },
      submitHandler: function (form) {
        form.submit();
      },
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

    addToListReminder() {
      this.turnOnLoading();
      var data = {
        name: this.newName,
      };
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      axios
        // api/remembers/{id}
        .put("api/remembers/" + this.id, data, options)
        .then((response) => {
          console.log("**** ", response);
          if (response.data !== null) {
            this.turnOffLoading();
            this.$toast.open({
              message: "Đã chỉnh sửa thành công",
              type: "success",
            });

            this.$emit("close-modal");
            this.$emit("update-success");
          }
        })
        .catch((error) => {
          console.log("AXIOS ERROR: ", error);
          this.turnOffLoading();

          this.$toast.open({
            message: "Thao tác thất bại",
            type: "success",
          });

          if (error.response.data.error === "Parameter error") {
            this.$toast.open({
              message: "Dữ liệu không hợp lệ vui lòng kiểm tra lại",
              type: "error",
            });

            return;
          }
        });
    },
  },
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
