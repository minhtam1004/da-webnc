<template>
  <section id="tables">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow>
          <mdb-card-body>
            <nav class="navbar navbar-expand-lg navbar-dark indigo mb-4">
              <!-- Navbar brand -->
              <a class="navbar-brand">
                <div class="form-row">
                  <div class="col-6">
                    <input
                      class="form-control"
                      type="text"
                      v-model="keyword"
                      placeholder="Nhập từ khóa"
                      aria-label="Nhập từ khóa"
                    />
                  </div>

                  <div class="col-4">
                    <select
                      class="browser-default custom-select"
                      data-style="btn-info"
                      v-model="selected"
                    >
                      <option value="2">Chưa thanh toán</option>
                      <option value="3">Đã thanh toán</option>
                      <option value="4">Đã hủy</option>
                      <option value="1">Tất cả</option>
                    </select>
                  </div>
                  <div class="col-2">
                    <button
                      type="button"
                      class="btn-floating purple-gradient px-3"
                      style="height:100%;border-radius: 0.5vmin"
                      @click="reload"
                    >
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                </div>
              </a>

              <!-- Collapsible content -->
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <form class="form-inline ml-auto">
                  <button
                    type="button"
                    class="btn btn-warning"
                    style="border-radius: 1vmin"
                    @click="load()"
                  >
                    <i class="fas fa-redo mr-1"></i>Tải lại
                  </button>

                  <button
                    type="button"
                    class="btn btn-success"
                    style="border-radius: 1vmin"
                    @click="showAddUser=true"
                  >
                    <i class="fas fa-plus-circle mr-1"></i>Thêm mới
                  </button>
                </form>
              </div>

              <!-- Collapsible content -->
            </nav>
            <div id="table" class="table-editable">
              <table class="table table-bordered table-responsive-md table-striped text-center">
                <thead>
                  <tr>
                    <th class="text-center">Mã nhắc nợ</th>
                    <th class="text-center">STK người nhắc nợ</th>
                    <th class="text-center">Số tiền nợ (VNĐ)</th>
                    <th class="text-center">Nội dung</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center" style="width: 12vmin;">Thời gian nhắc nợ</th>
                    <th class="text-center">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in pagination.data" :key="index">
                    <td class="pt-3-half">{{ item.id }}</td>
                    <td class="pt-3-half">{{ item.ownerId }}</td>
                    <td class="pt-3-half">{{ item.debt }}</td>
                    <td class="pt-3-half">{{ item.note}}</td>
                    <td class="pt-3-half">
                      <mdb-badge
                        v-if="item.status ==  'paid'"
                        color="success-color"
                        pill
                        class="pull-right"
                      >Đã thanh toán</mdb-badge>
                      <mdb-badge
                        v-else-if="item.status == 'deleted'"
                        color="danger-color"
                        pill
                        class="pull-right"
                      >Đã hủy</mdb-badge>
                      <mdb-badge v-else color="primary-color" pill class="pull-right">Chờ thanh toán</mdb-badge>
                    </td>
                    <td class="pt-3-half">{{ formatTime(item.created_at)}}</td>
                    <td>
                      <span class="table-remove">
                        <!-- <button
                          type="button"
                          class="btn btn-danger btn-rounded btn-sm my-0"
                          @click="showPopup(item.id)"
                        >
                          <i class="far f-atrash-alt"></i> Xóa
                        </button>-->

                        <button
                          type="button"
                          id="btn-one"
                          :disabled="loading"
                          class="btn btn-primary btn-rounded btn-sm my-0"
                          @click="$router.push({name: 'DebtDetail', params: { id: item.id }})"
                        >
                          <i class="far f-atrash-alt"></i> Chi tiết
                        </button>
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>

              <nav aria-label="Page navigation example">
                <ul class="pagination pg-blue" style="display: flex;justify-content: center;">
                  <li
                    :class="pagination.prev_page_url ? 'page-item' :  'page-item disabled'"
                    id="page1"
                    @click="clickPagination(1)"
                  >
                    <a class="page-link user-select" tabindex="-1">Trang trước</a>
                  </li>
                  <li class="page-item active" id="page2">
                    <a class="page-link user-select">
                      {{ pagination.current_page || 1 }}
                      <span class="sr-only"></span>
                    </a>
                  </li>
                  <li
                    :class="pagination.next_page_url ? 'page-item' : 'page-item disabled'"
                    id="page3"
                    @click="clickPagination(3)"
                  >
                    <a class="page-link user-select">Trang sau</a>
                  </li>
                </ul>
              </nav>
            </div>

            <!-- Editable table -->
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>

    <PaymentDebt :idPayment="idPaid" v-if="showPayment" @close-modal="showPayment=false" />
    <RemoveDebt :idDebt="idColum" v-if="showAddUser" @close-modal="closeModalRemoveDebt" />
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
import $ from "jquery";
// import Popup from "./Popup"
import RemoveDebt from "./Popup/RemoveDebt";
import PaymentDebt from "./Popup/PaymentDebt";
export default {
  name: "Tables",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    PaymentDebt,
    RemoveDebt,
    mdbBadge,
  },

  data() {
    return {
      data: [],
      filter: [],
      selected: 1,
      keyword: "",
      pagination: {
        data: [],
        per_page: 10,
        current_page: 1,
      },
      showAddUser: false,
      showPayment: false,
      loading: false,
    };
  },
  created() {
    this.load();
  },
  methods: {
    closeModalRemoveDebt() {
      this.showAddUser = false;
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
    showPopup(id) {
      console.log("aa");
      this.showAddUser = true;
      this.idColum = id;
    },
    formatTime(time) {
      const a = new Date(time);
      const month = a.getMonth() + 1;
      if (month < 10) {
        return a.getDate() + "/0" + month + "/" + a.getFullYear();
      }
      return a.getDate() + "/" + month + "/" + a.getFullYear();
    },
    reload() {
      this.filter = [];
      if (this.selected == 2) {
        this.filter.push("created");
      }
      if (this.selected == 3) {
        this.filter.push("paid");
      }
      if (this.selected == 4) {
        this.filter.push("deleted");
      }
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
        params: {
          status: this.filter,
        },
      };

      axios
        .get(
          "api/debt/other?limit=" +
            this.pagination.per_page +
            "&page=" +
            this.pagination.current_page,

          options
        )
        .then((response) => {
          console.log("nhac no 2: ", response);
          if (response.data !== null) {
            this.pagination = response.data;
          }
        })
        .catch((error) => {});
    },
    load() {
      this.reload();
    },
    clickPagination(id) {
      $("#page2").addClass("active").siblings().removeClass("active");
      if (id == 1 && this.pagination.prev_page_url != null) {
        console.log("Vo 1");
        this.pagination.current_page--;
        this.reload();
      }

      if (id == 3 && this.pagination.next_page_url != null) {
        console.log("Vo 2");
        this.pagination.current_page++;
        this.reload();
      }
    },
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.user-select {
  user-select: none;
}
.card.card-cascade .view.gradient-card-header {
  padding: 1rem 1rem;
  text-align: center;
}
.card.card-cascade h3,
.card.card-cascade h4 {
  margin-bottom: 0;
}
.pt-3-half {
  padding-top: 1.4rem;
}
</style>
