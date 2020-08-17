<template>
  <section id="tables">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Thông tin khách hàng</h4>
          </mdb-view>
          <mdb-card-body>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
              <li class="nav-item">
                <a
                  class="nav-link active"
                  id="pills-home-tab"
                  data-toggle="pill"
                  href="#pills-home"
                  role="tab"
                  aria-controls="pills-home"
                  aria-selected="true"
                >Thông tin cá nhân</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  id="pills-account-list-tab"
                  data-toggle="pill"
                  href="#pills-account-list"
                  role="tab"
                  aria-controls="pills-account-list"
                  aria-selected="false"
                >Danh sách tài khoản</a>
              </li>

            
              <li class="nav-item">
                <a
                  class="nav-link"
                  id="pills-transfer-tab"
                  data-toggle="pill"
                  href="#pills-transfer"
                  role="tab"
                  aria-controls="pills-transfer"
                  aria-selected="false"
                >Nạp tiền</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  id="pills-receive-money-tab"
                  data-toggle="pill"
                  href="#pills-receive-money"
                  role="tab"
                  aria-controls="pills-receive-money"
                  aria-selected="false"
                >Giao dịch nhận tiền</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  id="pills-profile-tab"
                  data-toggle="pill"
                  href="#pills-profile"
                  role="tab"
                  aria-controls="pills-profile"
                  aria-selected="false"
                >Giao dịch chuyển khoản</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  id="pills-contact-tab"
                  data-toggle="pill"
                  href="#pills-contact"
                  role="tab"
                  aria-controls="pills-contact"
                  aria-selected="false"
                >Giao dịch thanh toán nhắc nợ</a>
              </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
              <div
                class="tab-pane fade show active"
                id="pills-home"
                role="tabpanel"
                aria-labelledby="pills-home-tab"
              >
                <PersonalInfo />
              </div>
             

              <div
                class="tab-pane fade"
                id="pills-account-list"
                role="tabpanel"
                aria-labelledby="pills-account-list-tab"
              >
                <AccountList />
              </div>

              <div
                class="tab-pane fade"
                id="pills-transfer"
                role="tabpanel"
                aria-labelledby="pills-transfer-tab"
              >
                <Transfer />
              </div>
              <div
                class="tab-pane fade"
                id="pills-receive-money"
                role="tabpanel"
                aria-labelledby="pills-receive-money-tab"
              >
                <ReceivingMoney />
              </div>
              <div
                class="tab-pane fade"
                id="pills-profile"
                role="tabpanel"
                aria-labelledby="pills-profile-tab"
              >
                <ListTransfer />
              </div>

              <div
                class="tab-pane fade"
                id="pills-contact"
                role="tabpanel"
                aria-labelledby="pills-contact-tab"
              >
                <DebtPayment />
              </div>
            </div>
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>

<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";
import PersonalInfo from "./PersonalInfo";
import ReceivingMoney from "./Receiving";
import Transfer from "./Transfer";
import DebtPayment from "./DebtPayment";
import ListTransfer from "./ListTransfer";
import AccountList from "./AccountList";
export default {
  name: "Tables",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    PersonalInfo,
    ReceivingMoney,
    Transfer,
    ListTransfer,
    DebtPayment,
    AccountList
  },
  data() {
    return {};
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.card.card-cascade .view.gradient-card-header {
  padding: 1rem 1rem;
  text-align: center;
}
.card.card-cascade h3,
.card.card-cascade h4 {
  margin-bottom: 0;
}
</style>
