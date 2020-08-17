<template>
  <Popup @close-modal="closeAll" title="Thêm mới tài khoản tiết kiệm" minWidth="130vmin">
    <section id="registercustomer">
      <mdb-row>
        <mdb-col md="12">
          <mdb-card cascade narrow>
            <mdb-card-body style="height:65vh;overflow: auto">
              <form>
                <label for="password" class="grey-text">Tên khách hàng</label>
                <input type="text" v-model="data.name" class="form-control" readonly />
                <br />
                <label for="accountNumber" class="grey-text">Số tài khoản</label>
                <input
                  v-if="data.account"
                  type="number"
                  v-model="data.account[0].accountNumber"
                  class="form-control"
                  readonly
                />
                <br />

                <label for="amount" class="grey-text">Số tiền tiết kiệm</label>
                <input type="text" v-model="amount" class="form-control" />
                <br />
                <div style="display: flex;justify-content: flex-end;">
                  <div class="card" style="width: 50vmin;">
                    <div class="card-body" style>
                      <p
                        class="card-text blue-text"
                      >- Số tiền tối thiếu mở TK tiết kiệm: 1.000.000 VNĐ</p>
                      <p class="card-text blue-text">- Có thể mở nhiều TK tiết kiệm</p>
                    </div>
                  </div>
                </div>

                <div class="text-center mt-4">
                  <button
                    class="btn btn-unique"
                    :disabled="loading"
                    id="btn-one"
                    type="button"
                    @click="Topup"
                  >
                    <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                    Xác nhận
                  </button>
                </div>

                <Modal
                  v-if="showModal"
                  :type="typeModal"
                  :title="titleModal"
                  :message="messageModal"
                  @close-modal="showModal = false"
                />
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
import Modal from "./../Modal";
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
    Modal,
    Popup,
  },
  data() {
    return {
      username: "",
      id: this.$route.params.id,
      data: {
        account: null,
      },
      password: "",
      email: "",
      account: {
        accountNumber: "",
        excess: "",
      },
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
      reason: "",
      amount: 1000000,

      showModal: false,
      typeModal: "success",
      titleModal: "",
      messageModal: "",
    };
  },
  created() {
    this.load();
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
    load() {
      this.loading = true;
      this.turnOnLoading();
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      axios
        .get("api/bank/users/" + this.id, options)
        .then((response) => {
          this.loading = false;
          this.data = response.data;
          console.log("RESPONSE RECEIVED: ", response);
        })
        .catch((error) => {
          this.loading = false;
          console.log("AXIOS ERROR: ", error);
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
    Topup() {
      this.loading = true;
      this.turnOnLoading();
      const options = {
        headers: {
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      var data = {
        amount: this.amount,
      };
      axios
        .post("api/bank/users/" + this.id + "/accounts", data, options)
        .then((response) => {
          if ((response.status = 200)) {
            console.log("RESPONSE RECEIVED: ", response);
            this.$toast.open({
              message: "Thêm tài khoản tiết kiệm thành công",
              type: "success",
            });
            this.loading = false;
            this.turnOffLoading();
            this.$emit("close-modal");
            this.$emit("add-success");
          }
        })
        .catch((error) => {
          this.loading = false;
          this.turnOffLoading();
          if (error.response.status === 422) {
            this.$toast.open({
              message: "Dữ liệu không chính xác",
              type: "danger",
            });
          }
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
