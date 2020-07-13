<template>
  <section id="registercustomer">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow>
          <mdb-card-body style="height:80vh;overflow: auto">
            <form>
              <label for="username" class="grey-text">Tên đăng nhập</label>
              <input type="text" v-model="data.username" class="form-control" readonly />
              <br />
              <label for="password" class="grey-text">Tên nhân viên</label>
              <input type="text" v-model="data.name" class="form-control" readonly />
              <br />
              <label for="password" class="grey-text">Quyền hạn</label>
              <select
                class="browser-default custom-select"
                data-style="btn-info"
                style="margin-bottom: 3vmin;"
                v-model="selected"
              >
                <option value="1">Nhân viên</option>
                <option value="2">Quản trị viên</option>
               
              </select>

              <br />
              <label for="name" class="grey-text">Địa chỉ email</label>
              <input type="text" v-model="data.email" class="form-control" readonly />
              <br />
              <label for="phone" class="grey-text">Số điện thoại</label>
              <input type="text" id="phone" v-model="data.phone" class="form-control" readonly />
                <div class="text-center mt-4">
                <button
                  class="btn btn-unique"
                  :disabled="loading"
                  id="btn-one"
                  type="button"
                  @click="checkValidateRegisterForm"
                >
                  <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                  Xác nhận
                </button>
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
export default {
  name: "Profile",
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
      id: this.$route.params.id,
      data: {
        account: null
      },
      selected: 1,
      password: "",
      email: "",
      account: {
        accountNumber: "",
        excess: ""
      },
      name: "",
      phone: "",
      message: "",
      show: false,
      loading: false,
      msg: [],
      loginForm: {
        recaptchaVerified: false,
        pleaseTickRecaptchaMessage: ""
      },
      listPermission: [
        { id: 1, select: "selected", name: "Quản trị viên" },
        { id: 2, select: "", name: "Nhân viên ngân hàng" }
      ]
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
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };
      axios
        .get("http://127.0.0.1:8000/api/bank/users/" + this.id, options)
        .then(response => {
          this.loading = false;
          this.data = response.data;
          console.log("RESPONSE RECEIVED: ", response);
        })
        .catch(error => {
          this.loading = false;
          console.log("AXIOS ERROR: ", error);
        });
    },
  }
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
