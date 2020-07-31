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
                <label for="accountNumber" class="grey-text">Số tài khoản</label>
                <input v-if="data.account" type="text" v-model="data.account.accountNumber" class="form-control" readonly />
                <br />
                <label for="excess" class="grey-text">Số dư</label>
                <input v-if="data.account" type="text" v-model="data.account.excess" class="form-control" readonly />
                <br />
                <label for="password" class="grey-text">Tên khách hàng</label>
                <input type="text" v-model="data.name" class="form-control" readonly />
                <br />
                <label for="name" class="grey-text">Địa chỉ email</label>
                <input type="text" v-model="data.email" class="form-control" readonly />
                <br />
                <label for="phone" class="grey-text">Số điện thoại</label>
                <input type="text" id="phone" v-model="data.phone" class="form-control" readonly />
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
      password: "",
      email: "",
      account: {
      accountNumber: "",
      excess: ""},
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
  created() {
    this.load()
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
        .get("api/bank/users/" + this.id, options)
        .then(response => {
          this.data = response.data;
          console.log("RESPONSE RECEIVED: ", response);
          
        })
        .catch(error => {
        
          console.log("AXIOS ERROR: ", error);
       
        });
    },
    getUser() {
      console.log("555")
      axios
        .get("api/bank/users/" + this.id, {
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
