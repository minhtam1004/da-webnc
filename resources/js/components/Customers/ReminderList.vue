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
                  <button
                    type="button"
                    class="btn btn-success"
                    style="border-radius: 1vmin"
                    @click="showSavingAccount=true"
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
                    <th class="text-center">Số tài khoản</th>
                    <th class="text-center">Tên gợi nhớ</th>
                    <th class="text-center">Tên ngân hàng</th>
                    <th class="text-center">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in pagination.data" :key="index">
                    <td class="pt-3-half">{{ item.accountId }}</td>
                    <td class="pt-3-half">{{ item.name }}</td>
                    <td class="pt-3-half">
                      {{ item.bankId ? (item.bankId == 1 ? "Ngân hàng nhóm 34" :
                      "Ngân hàng nhóm 5") : "" }}
                    </td>
                    <td>
                      <span class="table-remove">
                        <button
                          type="button"
                          class="btn btn-danger pa-4"
                          @click="deleteReminder(item.id)"
                        >
                          <i class="fas fa-trash-alt" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-primary pa-4" @click="update(item)">
                          <i class="fas fa-pencil-alt" aria-hidden="true"></i>
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

    <UpdateReminder
      :name="nameReminder"
      :id="idReminder"
      v-if="showAddReminder"
      @close-modal="showAddReminder = false"
      @update-list="reload"
    />
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
// import Popup from "./Popup"
import UpdateReminder from "./../Popup/UpdateReminder";
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
    UpdateReminder,
  },

  data() {
    return {
      data: [],
      id: this.$route.params.id,
      keyword: "",
      pagination: {
        data: [],
        per_page: 10,
        current_page: 1,
        last_page: 1,
      },
      showAddUser: false,
      showAddReminder: false
    };
  },
  created() {
    this.load();
  },
  methods: {
    formatTime(time) {
      const a = new Date(time);
      const month = a.getMonth() + 1;
      if (month < 10) {
        return a.getDate() + "/0" + month + "/" + a.getFullYear();
      }
      return a.getDate() + "/" + month + "/" + a.getFullYear();
    },
    update(t) {
      this.showAddReminder = true;
      this.nameReminder = t.name;
      this.idReminder = t.id;
    },
    deleteReminder(t) {
      console.log("&&&", t);
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };

      axios
        .delete("api/remembers/" + t, options)
        .then((response) => {
          console.log("====", response);
          this.$toast.open({
            message: "Xóa thành công",
            type: "success",
          });
          this.reload();
          this.search("");
        })
        .catch((error) => {
          this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error",
          });
        });
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
        .post("api/remembers/search", {}, options)

        .then((response) => {
          console.log("danh sach: ", response);
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
