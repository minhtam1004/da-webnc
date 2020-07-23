<template>
  <section id="tables">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <!-- <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Danh sách khách hàng</h4>
          </mdb-view>-->
          <mdb-card-body>
            <nav class="navbar navbar-expand-lg navbar-dark indigo mb-4">
              <!-- Navbar brand -->
              <a class="navbar-brand">
                <div class="form-row">
                  <div class="col" style="width:45vmin">
                    <input
                      class="form-control"
                      type="date"
                      v-model="startDate"
                      id="example-date-input"
                    />
                  </div>

                  <div class="col" style="width:45vmin">
                    <input
                      class="form-control"
                      type="date"
                      v-model="endDate"
                      id="example-date-input"
                    />
                  </div>

                  <div class="col">
                    <button
                      type="button"
                      class="btn-floating purple-gradient px-3"
                      style="height:100%;border-radius: 0.5vmin"
                      @click="getTransaction"
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

            <div class="md-form">
              <div id="table" class="table-editable">
                <table class="table table-bordered table-responsive-md table-striped text-center">
                  <thead>
                    <tr>
                      <th class="text-center">Mã giao dịch</th>
                      <th class="text-center">Người gửi</th>
                      <th class="text-center">Người nhận</th>
                      <th class="text-center">Số tiền</th>
                      <th class="text-center">Trạng thái</th>
                      <th class="text-center">Thời gian giao dịch</th>

                      <th class="text-center">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in pagination.data" :key="index">
                      <td class="pt-3-half">{{ item.id }}</td>
                      <td class="pt-3-half">{{ item.sendId }}</td>
                      <td class="pt-3-half">{{ item.receivedId }}</td>
                      <td class="pt-3-half">{{ item.amount }}</td>
                      <td class="pt-3-half">{{ item.isConfirm == 1 ? 'Thành công' : 'Thất bại' }}</td>
                      <td class="pt-3-half">{{ formatTime(item.created_at)}}</td>
                      <td>
                        <span class="table-remove">
                          <button
                            type="button"
                            class="btn btn-info btn-rounded btn-sm my-0"
                            @click="$router.push({name: 'CustomerDetail', params: { id: item.id }})"
                          >Chi tiết</button>
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
                        {{ pagination.current_page }}
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
            </div>

            <!-- Editable table -->
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>
<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";
import $ from "jquery";

export default {
  name: "Tables",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
  },

  data() {
    return {
      data: [],
      date: "",
      startDate: "",
      id: this.$route.params.id,
      endDate: new Date(),
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
    const a = new Date();
    const month = a.getMonth() + 1;
    if (a.getMonth() < 10) {
      this.startDate =
        a.getFullYear() + "-0" + month + "-" + a.getDate();
      this.endDate = a.getFullYear() + "-0" + month + "-" + a.getDate();
    } else {
      this.startDate = a.getFullYear() + "-" + month + "-" + a.getDate();
      this.endDate = a.getFullYear() + "-" + month + "-" + a.getDate();
    }
  },
  methods: {
    formatTime(time) {
      const a = new Date(time);
      if (a.getMonth() < 10) {
        return a.getDate() + "/0" + a.getMonth() + "/" + a.getFullYear();
      }
      return a.getDate() + "/" + a.getMonth() + "/" + a.getFullYear();
    },
    reload() {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      console.log(this.startDate);
      console.log(this.endDate);
      axios
        .get(
          "api/banks/" +
            this.id +
            "/transfers?keyword=" +
            this.keyword +
            "&limit=" +
            this.pagination.per_page +
            "&page=" +
            this.pagination.current_page,
          options
        )
        .then((response) => {
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.pagination = response.data;
          }
        })
        .catch((error) => {});
    },
    formatDateTime(dateTime) {
      var date = dateTime.split("-");
      var reverseArray = date.reverse();
      return reverseArray.join("/");
    },
    getTransaction() {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      axios
        .get(
          "api/banks/" +
            this.id +
            "/transfers?start=" +
            this.formatDateTime(this.startDate) +
            "&end=" +
            this.formatDateTime(this.endDate) +
            "&keyword=" +
            this.keyword +
            "&limit=" +
            this.pagination.per_page +
            "&page=" +
            this.pagination.current_page,
          options
        )
        .then((response) => {
          console.log("RESPONSE RECEIVED 2: ", response);
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

label {
  margin-left: 20px;
}
#datepicker {
  width: 20vmin;
  margin: 0 20px 20px 20px;
}
#datepicker > span:hover {
  cursor: pointer;
}
</style>
