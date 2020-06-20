<template>
  <section id="profile">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Thông tin cá nhân</h4>
          </mdb-view>
          <mdb-card-body>
            <form>
              <label for="name" class="grey-text">Họ và tên</label>
              <input type="text" v-model="profile.name" readonly class="form-control" />
              <br />
              <label for="phone" class="grey-text">Số điện thoại</label>
              <input type="text" readonly v-model="profile.phone" class="form-control" />
              <br />
              <label for="email" class="grey-text">Địa chỉ email</label>
              <input type="email" readonly v-model="profile.email" class="form-control" />
            </form>
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>

<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from 'mdbvue'
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
      profile: this.$store.state.user.authUser,
      date: ""
    };
  },
  created() {
    console.log("mimi", this.$store.state.user.authUser);
  },
  methods: {
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
