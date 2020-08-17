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
                  <div class="col-10">
                    <input
                      class="form-control"
                      type="text"
                      v-model="keyword"
                      placeholder="Nhập từ khóa"
                      aria-label="Nhập từ khóa"
                    />
                  </div>
                  <div class="col">
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
                    @click="load"
                  >
                    <i class="fas fa-redo mr-1"></i>Tải lại
                  </button>
                </form>
              </div>

              <!-- Collapsible content -->
            </nav>
            <div id="table" class="table-editable">
              <table class="table table-bordered table-responsive-md table-striped text-center">
                <thead>
                  <tr>
                    <th class="text-center">Mã giao dịch</th>
                    <th class="text-center">Số tài khoản gửi</th>
                    <th class="text-center">Tên chủ tài khoản</th>
                    <th class="text-center">Tên ngân hàng</th>
                    <th class="text-center">Số tiền (VNĐ)</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center">Thời gian giao dịch</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in pagination.data" :key="index">
                    <td class="pt-3-half">{{ item.id }}</td>
                    <td class="pt-3-half">{{ item.sendId }}</td>
                    <td class="pt-3-half">{{ item.sendId }}</td>
                    <td class="pt-3-half">{{ item.sendId }}</td>
                    <td class="pt-3-half">{{ item.amount }}</td>
                    <td class="pt-3-half">
                      <mdb-badge
                        color="success-color"
                        pill
                        class="pull-right"
                      >Thành công</mdb-badge>
                      <!-- <mdb-badge v-else color="danger-color" pill class="pull-right">Chờ xác nhận</mdb-badge> -->
                      <!-- <mdb-badge color="primary-color" pill class="pull-right">14</mdb-badge> -->
                    </td>
                    <td class="pt-3-half">{{ formatTime(item.created_at) }}</td>
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
  mdbIcon,
} from "mdbvue";
// import Popup from "./Popup"

export default {
  name: "Tables",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    mdbBadge,
    mdbIcon,
  },

  data() {
    return {
      data: [],
      keyword: "",
      pagination: {
        data: [],
        per_page: 10,
        current_page: 1,
        last_page: 1,
      },
      showAddUser: false,
    };
  },
  created() {
    this.load();
  },
  methods: {
      getUser(id) {
      console.log("555")
      axios
        .get("api/bank/users/" + id, {
          headers: {
            Authorization: "bearer" + this.$store.state.user.access_token
          }
        })
        .then(response => {
          console.log(response);
          
        })
        .catch(error => {
          return this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error"
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
    reload() {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      // api/bank/users/{id / me}/recharge-transfers) (data: limit/page

      axios
        .get(
          "api/bank/users/me/transfers?limit=" +
            this.pagination.per_page +
            "&page=" +
            this.pagination.current_page,
          options
        )
        .then((response) => {
          console.log("RESPONSE RECEIVED 1: ", response);
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
