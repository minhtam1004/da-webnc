<template>
  <section id="registercustomer">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow>
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Thông tin chi tiết nhắc nợ</h4>
          </mdb-view>
          <mdb-card-body style="height:80vh;overflow: auto">
            <form>
              <label for="username" class="grey-text">STK người nhắc nợ</label>
              <input type="text" v-model="data.ownerId" class="form-control" readonly />
              <br />
              <label for="accountNumber" class="grey-text">Số tiền nợ</label>
              <input type="text" v-model="data.debt" class="form-control" readonly />
              <br />
              <label for="excess" class="grey-text">Ngày tạo</label>
              <input type="text" v-model="data.created_at" class="form-control" readonly />
              <br />
              <label for="password" class="grey-text">Trạng thái</label>
              <br />
              <mdb-badge
                v-if="data.status ==  'paid'"
                color="success-color"
                pill
                class="pull-right"
              >Đã thanh toán</mdb-badge>
              <mdb-badge
                v-else-if="data.status == 'deleted'"
                color="danger-color"
                pill
                class="pull-right"
              >Đã hủy</mdb-badge>
              <mdb-badge v-else color="primary-color" pill class="pull-right">Chờ thanh toán</mdb-badge>

              <br />
              <br />
              <label for="name" class="grey-text">Ghi chú</label>
              <textarea class="form-control rounded-0" v-model="data.note" rows="3" readonly></textarea>
              <br />
              <label v-if="data.deleteNote" for="phone" class="grey-text">Lí do xóa</label>
              <textarea
                v-if="data.deleteNote"
                class="form-control rounded-0"
                v-model="data.deleteNote"
                rows="3"
                readonly
              ></textarea>

              <div class="text-center mt-4">
                <button
                  class="btn btn-danger"
                  :disabled="loadingError"
                  id="btn-error"
                  type="button"
                  v-if="data.status !=  'deleted'"
                  @click="showAddUser = true"
                >
                  <i v-if="loadingError" class="fa fa-spinner fa-spin"></i>
                  Hủy nhắc nợ
                </button>

                <button
                  class="btn btn-success"
                  :disabled="loading"
                  id="btn-one"
                  type="button"
                  v-if="data.status ==  'created' && idUser != data.ownerId"
                  @click="paymentDebt"
                >
                  <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                  Thanh toán
                </button>
              </div>
            </form>
            <PaymentDebt :idPayment="idPaid" v-if="showPayment" @close-modal="showPayment=false" />
            <RemoveDebt :idDebt="id" v-if="showAddUser" @close-modal="showAddUser = false;" />
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>

<script>
import {
  mdbRow,
  mdbCol,
  mdbCard,
  mdbView,
  mdbCardBody,
  mdbTbl,
  mdbBadge,
} from "mdbvue";
import PaymentDebt from "./Popup/PaymentDebt";
import RemoveDebt from "./Popup/RemoveDebt";
export default {
  name: "Profile",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    mdbBadge,
    PaymentDebt,
    RemoveDebt,
  },
  data() {
    return {
      username: "",
      showPayment: false,
      id: this.$route.params.id,
      idUser: this.$store.state.user.authUser.account.accountNumber,
      showAddUser: false,
      idPaid: null,
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
      loadingError: false,
      msg: [],
      loginForm: {
        recaptchaVerified: false,
        pleaseTickRecaptchaMessage: "",
      },
    };
  },
  created() {
    this.load();
  },
  methods: {
    paymentDebt() {
      this.turnOnLoading();
      this.loading = true;

      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };

      axios
        // POST api/debt/{id}/paid
        .post("api/debt/" + this.id + "/paid", {}, options)
        .then((response) => {
          this.turnOffLoading();
          this.loading = false;
          this.idPaid = response.data.transferId;
          this.showPayment = true;
        })
        .catch((error) => {
          this.turnOffLoading();
          this.loading = false;
          this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "danger",
          });
        });
    },
    formatTime(time) {
      const a = new Date(time);
      const month = a.getMonth() + 1;
      if (month < 10) {
        return a.getDate() + "/0" + month + "/" + a.getFullYear();
      }
      return a.getDate() + "/" + month + "/" + a.getFullYear();
    },
    closeAll() {
      this.$emit("close-modal");
    },
    turnOnLoading() {
      $("#btn-one")
        .html(
          '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Thanh toán'
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
    load() {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      axios
        .get("api/debt/" + this.id, options)
        .then((response) => {
          this.data = response.data;
          this.data.created_at = this.formatTime(this.data.created_at);
          console.log("RESPONSE RECEIVED: ", response);
        })
        .catch((error) => {
          console.log("AXIOS ERROR: ", error);
        });
    },
    getUser() {
      console.log("555");
      axios
        .get("api/bank/users/" + this.id, {
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
