<template>
  <section id="tables">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Danh sách nhân viên</h4>
          </mdb-view>
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
                      placeholder="Nhập tên nhân viên"
                      aria-label="Tên nhân viên"
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
                    <th class="text-center">Mã nhân viên</th>
                    <th class="text-center">Tên nhân viên</th>
                    <th class="text-center">Tên đăng nhập</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Số điện thoại</th>
                    <th class="text-center">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in pagination.data" :key="index">
                    <td class="pt-3-half">{{ item.id }}</td>
                    <td class="pt-3-half">{{ item.name }}</td>
                    <td class="pt-3-half">{{ item.username }}</td>
                    <td class="pt-3-half">{{ item.email }}</td>
                    <td class="pt-3-half">{{ item.phone}}</td>
                    <td>
                      <span class="table-remove">
                        <button
                          type="button"
                          class="btn btn-primary btn-rounded btn-sm my-0"
                          @click="$router.push({name: 'EmployeeDetail', params: { id: item.id }})"
                        >
                          <i class="fas fa-marker"></i>
                        </button>
                        <button
                          type="button"
                          class="btn btn-danger btn-rounded btn-sm my-0"
                          @click="deleteEmployee(item)"
                        >
                          <i class="fas fa-trash-alt"></i>
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

    <AddEmployee v-if="showAddUser" @close-modal="closePopup()" />
  </section>
</template>

<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";
// import Popup from "./Popup"
import AddEmployee from "./Popup/AddEmployee";
export default {
  name: "Tables",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    AddEmployee
  },

  data() {
    return {
      data: [],
      keyword: "",
      pagination: {
        data: [],
        per_page: 10,
        current_page: 1
      },
      showAddUser: false
    };
  },
  created() {
    this.load();
  },
  methods: {
    deleteEmployee(t) {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };

      axios
        .delete("api/bank/employees/" + t.id, options)
        .then(response => {
          console.log("RESPONSE RECEIVED: ", response);
          this.$toast.open({
            message: "Nhân viên đã được xóa",
            type: "success"
          });
          this.load();
        })
        .catch(error => {
          this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error"
          });
        });
    },
    closePopup() {
      this.showAddUser = false;
      this.load();
    },
    reload() {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };

      if (this.keyword.length > 0) {
        axios
          .get("api/bank/employees?keyword=" + this.keyword, options)
          .then(response => {
            console.log("RESPONSE RECEIVED: ", response);
            if (response.data !== null) {
              this.pagination = response.data;
            }
          })
          .catch(error => {});
      } else {
        axios
          .get(
            "api/bank/employees?limit=" +
              this.pagination.per_page +
              "&page=" +
              this.pagination.current_page,
            options
          )
          .then(response => {
            console.log("RESPONSE RECEIVED: ", response);
            if (response.data !== null) {
              this.pagination = response.data;
            }
          })
          .catch(error => {});
      }
    },
    load() {
      this.reload();
    },
    clickPagination(id) {
      $("#page2")
        .addClass("active")
        .siblings()
        .removeClass("active");
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
    }
  }
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
