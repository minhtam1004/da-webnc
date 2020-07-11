<template>
<Popup @close-modal="closeAll" title="Thêm mới khách hàng">
  <section id="profile">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow>
       
          <mdb-card-body style="height:80vh;overflow: auto">
            <form>
              <label for="username" class="grey-text">Tên đăng nhập</label>
              <input type="text" v-model="username" class="form-control" />
              <br />
              <label for="password" class="grey-text">Mật khẩu</label>
              <input type="password" v-model="password" class="form-control" />
              <br />
              <label for="name" class="grey-text">Họ và tên</label>
              <input type="text" v-model="name" class="form-control" />
              <br />
              <label for="phone" class="grey-text">Số điện thoại</label>
              <input type="text" v-model="phone" class="form-control" />
              <br />
              <label for="email" class="grey-text">Địa chỉ email</label>
              <input type="email" v-model="email" class="form-control" />

               <div class="text-center mt-4">
                <button class="btn btn-unique" type="button" id="btn-one" @click="registerCustomer">Đăng kí</button>
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
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from 'mdbvue'
import Popup from "./Popup"
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
      password: "",
      email: "",
      phone: ""
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
    registerCustomer() {
     this.turnOnLoading();
    
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
      axios
        .post("api/bank/customers", data, options)
        .then(response => {
          this.turnOffLoading();
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.$toast.open({
              message: "Thêm mới khách hàng thành công",
              type: "success"
            });
            this.$emit("close-modal");
          }
        })
        .catch(error => {
          this.turnOffLoading();
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
