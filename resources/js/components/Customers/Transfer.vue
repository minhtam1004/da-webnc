<template>
  <section id="registercustomer">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow>
          <mdb-card-body style="height:80vh;overflow: auto">
            <form>
              <label for="password" class="grey-text">Tên khách hàng</label>
              <input type="text" v-model="data.name" class="form-control" readonly />
              <br />
              <label for="accountNumber" class="grey-text">Số tài khoản</label>
              <input
                v-if="data.account"
                type="text"
                v-model="data.account.accountNumber"
                class="form-control"
                readonly
              />
              <br />

              <label for="amount" class="grey-text">Số tiền nạp</label>
              <input type="text" v-model="amount" class="form-control" />
              <br />

              <label for="reason" class="grey-text">Ghi chú</label>
              <textarea class="form-control rounded-0" v-model="reason" rows="3"></textarea>

              <div class="text-center mt-4">
                <button
                  class="btn btn-unique"
                  :disabled="loading"
                  id="btn-one"
                  type="button"
                  @click="Topup"
                >
                  <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                  Nạp tiền
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
</template>

<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";
import Modal from "./../Modal";
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
      amount: 50000,

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
      const options = {
        headers: {
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      var data = {
        amount: this.amount,
        reason: this.reason,
      };
      axios
        .post(
          "api/bank/accounts/" + this.data.account.accountNumber + "/recharge",
          data,
          options
        )
        .then((response) => {
          console.log("RESPONSE RECEIVED: ", response);
          this.$toast.open({
            message: "Nạp tiền thành công",
            type: "success",
          });
        })
        .catch((error) => {
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
