<template>
  <section id="accountinfo">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Thông tin tài khoản</h4>
          </mdb-view>
          <mdb-card-body>
            <form>
              <label for="username" class="grey-text">Tên đăng nhập</label>
              <input type="text" readonly v-model="account.username" class="form-control" />
              <br />

              <label for="accountnumber" class="grey-text">Số tài khoản</label>
              <input type="text" v-model="account.account.accountNumber" readonly class="form-control" />
              <br />

              <label for="excess" class="grey-text">Số dư</label>
              <input type="text" readonly v-model="account.account.excess" class="form-control" />
              <br />

              <div class="text-center mt-4">
                <button class="btn btn btn-danger" type="button" @click="showChangePass = true">Đổi mật khẩu</button>
              </div>

            </form>
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
    <ChangePassword v-if="showChangePass" @close-modal="showChangePass=false"  />
  </section>
</template>
<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";
import ChangePassword from "./Popup/ChangePassword";
export default {
  name: "AccountInfo",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    ChangePassword
  },
  data() {
    return {
      account: this.$store.state.user.authUser,
      showChangePass: false
    };
  },
  created() {
    console.log(this.$store.state.user.authUser)
  },
  methods: {}
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
