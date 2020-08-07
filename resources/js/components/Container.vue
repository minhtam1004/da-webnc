<template>
  <div class="flexible-content">
    <!--Navbar-->
    <nav class="mb-1 navbar navbar-expand-lg navbar-dark primary-color">
      <a class="navbar-brand" style="margin-left: 7vmin;color:white">KLXBank</a>
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarSupportedContent-333"
        aria-controls="navbarSupportedContent-333"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent-333">
        <ul class="navbar-nav mr-auto">
          <!-- <li class="nav-item active">
            <a class="nav-link" href="#">
              Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Features</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Pricing</a>
          </li> -->
          
        </ul>
        <ul class="navbar-nav ml-auto nav-flex-icons">
          <li class="nav-item dropdown" style="position:relative">
            <a
              class="nav-link dropdown-toggle"
              id="navbarDropdownMenuLink-333"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <mdb-icon icon="bell" />
              
            </a>
            <div v-if="countNotify > 0" class="badge">{{ countNotify }}</div>
            <div
              class="dropdown-menu dropdown-menu-right dropdown-default notify"
              aria-labelledby="navbarDropdownMenuLink-333"
            >
              <a class="dropdown-item">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col"></th>

                      <th
                        scope="col"
                        style="display: flex;justify-content: flex-end;margin-top: 0vmin; marrgin-bottom: 0vmin"
                      >
                        <a
                          @click="readAll"
                          style="color: #33B5E5;text-decoration: underline;"
                        >Đánh dấu tất cả đã đọc</a>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="item in notify"
                      :key="item.id"
                      :class="item.readAt ? '' : 'background-colum'"
                      @click="viewDebtDetail(item.id, item.data.debtId)"
                    >
                      <th scope="row">
                        <button
                          v-if="item.data.debtType == 'created'"
                          type="button"
                          class="btn btn-primary px-5"
                        >
                          <i class="fas fa-plus-circle" aria-hidden="true"></i>
                        </button>
                        <button
                          v-else-if="item.data.debtType == 'paid'"
                          type="button"
                          class="btn btn-success px-5"
                        >
                          <i class="fas fa-check-circle" aria-hidden="true"></i>
                        </button>
                        <button v-else type="button" class="btn btn-danger px-5">
                          <i class="fas fa-minus-circle" aria-hidden="true"></i>
                        </button>
                      </th>
                      <td
                        v-if="item.data.debtType == 'created'"
                      >{{ "Bạn nhận được lời nhắc nợ từ STK: " + item.data.account.accountNumber }}</td>
                      <td
                        v-else-if="item.data.debtType == 'paid'"
                      >{{ "STK: " + item.data.account.accountNumber + " đã thanh toán nợ" }}</td>
                      <td v-else>{{ "STK: " + item.data.account.accountNumber + "đã xóa nhắc nợ" }}</td>
                    </tr>
                  </tbody>
                </table>
              </a>
            </div>
          </li>

          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              id="navbarDropdownMenuLink-333"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="fas fa-user"></i>
            </a>
            <div
              class="dropdown-menu dropdown-menu-right dropdown-default"
              aria-labelledby="navbarDropdownMenuLink-333"
              style="position: absolute!important;"
            >
              <a class="dropdown-item" @click="redirectProfile">Hồ sơ</a>
              <a class="dropdown-item" @click="logout">Đăng xuất</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
    <!-- <mdb-navbar class="flexible-navbar white" light position="top" scrolling>
      <mdb-navbar-brand target="_blank" style="margin-left: 7vmin;">KLXBank</mdb-navbar-brand>
      <mdb-navbar-toggler>
        <mdb-navbar-nav left>
          <mdb-nav-item to="/dashboard" waves-fixed active class="active">Dashborad</mdb-nav-item>
          <mdb-nav-item waves-fixed to="/profile">Thông tin cá nhân</mdb-nav-item>
          <mdb-nav-item to="/account-info" waves-fixed>Thông tin tài khoản</mdb-nav-item>
          <mdb-nav-item to="/transactions" waves-fixed>Lịch sử giao dịch</mdb-nav-item>
        </mdb-navbar-nav>
        <mdb-navbar-nav right style="display: flex;align-items: center;">
          <div style="position: relative;margin-right: 2vmin">
            <mdb-btn
              tag="a"
              gradient="peach"
              floating
              size="lg"
              style="border-radius: 50%;margin-top: 1vmin;"
            >
              <mdb-icon icon="bell" />
            </mdb-btn>

            <mdb-badge
              color="danger"
              class="ml-2"
              style="position: absolute;top: 0;right: 0"
            >{{ notify.length }}</mdb-badge>
          </div>

          <mdb-btn tag="a" gradient="blue" floating size="sm" @click="logout()">
            <mdb-icon icon="sign-out-alt" />
          </mdb-btn>
        </mdb-navbar-nav>
      </mdb-navbar-toggler>
    </mdb-navbar>-->
    <!-- <nav class="navbar navbar-expand-lg navbar-light bg-light" style="display: flex;">
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarNavDropdown"
        aria-controls="navbarNavDropdown"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown" style="place-content: flex-end;">
        <ul class="navbar-nav">
          <li class="nav-item active"></li>
          <li class="nav-item dropdown edit-dropdown">
            <a
              class="nav-link dropdown-toggle"
              id="navbarDropdownMenuLink"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <img
                src="https://mdbootstrap.com/img/Others/documentation/img%20(75)-mini.jpg"
                alt="thumbnail"
                class="img-thumbnail"
                style="width: 40px;height:40px;border-radius: 50%"
              />
              {{ $store.state.user.authUser.name}}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" @click="logout()">Đăng xuất</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>-->
    <!--/.Navbar-->
    <!-- Sidebar -->
    <div class="sidebar-fixed position-fixed">
      <a class="logo-wrapper">
        <img alt class="img-fluid" />
      </a>
      <mdb-list-group class="list-group-flush">
        <router-link to="/dashboard" @click.native="activeItem = 1" v-if="permission == 'admin'">
          <mdb-list-group-item :action="true" :class="activeItem === 1 && 'active'">
            <mdb-icon icon="chart-pie" class="mr-3" />Dashboard
          </mdb-list-group-item>
        </router-link>
        <router-link to="/profile" @click.native="activeItem = 2">
          <mdb-list-group-item :action="true" :class="activeItem === 2 && 'active'">
            <mdb-icon icon="user" class="mr-3" />Thông tin cá nhân
          </mdb-list-group-item>
        </router-link>
        <router-link to="/account-info" @click.native="activeItem = 3" v-if="permission == 'user'">
          <mdb-list-group-item :action="true" :class="activeItem === 3 && 'active'">
            <mdb-icon icon="user-md" class="mr-3" />Thông tin tài
            khoản
          </mdb-list-group-item>
        </router-link>
        <router-link to="/transfers" @click.native="activeItem = 4" v-if="permission == 'user'">
          <mdb-list-group-item :action="true" :class="activeItem === 4 && 'active'">
            <mdb-icon far icon="money-bill-alt" class="mr-3" />Chuyển khoản nội bộ
          </mdb-list-group-item>
        </router-link>

        <router-link
          to="/transfers-interbank"
          @click.native="activeItem = 5"
          v-if="permission == 'user'"
        >
          <mdb-list-group-item :action="true" :class="activeItem === 5 && 'active'">
            <mdb-icon icon="university" class="mr-3" />Chuyển khoản liên ngân hàng
          </mdb-list-group-item>
        </router-link>

        <router-link
          to="/add-debt-reminder"
          @click.native="activeItem = 6"
          v-if="permission == 'user'"
        >
          <mdb-list-group-item :action="true" :class="activeItem === 6 && 'active'">
            <mdb-icon icon="bell" class="mr-3" />Tạo nhắc nợ
          </mdb-list-group-item>
        </router-link>

        <router-link to="/employees" @click.native="activeItem = 7" v-if="permission == 'admin'">
          <mdb-list-group-item :action="true" :class="activeItem === 7 && 'active'">
            <mdb-icon icon="user-secret" class="mr-3" />Quản lí nhân viên
          </mdb-list-group-item>
        </router-link>

        <router-link to="/customers" @click.native="activeItem = 8" v-if="permission == 'editor'">
          <mdb-list-group-item :action="true" :class="activeItem === 8 && 'active'">
            <mdb-icon icon="users" class="mr-3" />Quản lí khách hàng
          </mdb-list-group-item>
        </router-link>

        <router-link to="/transactions" @click.native="activeItem = 9" v-if="permission == 'user'">
          <mdb-list-group-item :action="true" :class="activeItem === 9 && 'active'">
            <mdb-icon icon="exchange-alt" class="mr-3" />Danh sách giao dịch
          </mdb-list-group-item>
        </router-link>

        <router-link to="/banks" @click.native="activeItem = 10" v-if="permission == 'admin'">
          <mdb-list-group-item :action="true" :class="activeItem === 10 && 'active'">
            <mdb-icon icon="list-alt" class="mr-3" />Danh sách ngân hàng liên kết
          </mdb-list-group-item>
        </router-link>

        <router-link to="/debt-remider" @click.native="activeItem = 11" v-if="permission == 'user'">
          <mdb-list-group-item :action="true" :class="activeItem === 11 && 'active'">
            <mdb-icon icon="list-alt" class="mr-3" />Danh sách nợ
          </mdb-list-group-item>
        </router-link>
        <!-- <router-link to="/maps" @click.native="activeItem = 6">
          <mdb-list-group-item :action="true" :class="activeItem === 6 && 'active'">
            <mdb-icon icon="map" class="mr-3" />Maps
          </mdb-list-group-item>
        </router-link>-->
        <router-link
          to="/register-customer"
          @click.native="activeItem = 12"
          v-if="permission == 'editor'"
        >
          <mdb-list-group-item :action="true" :class="activeItem === 12 && 'active'">
            <mdb-icon icon="user-plus" class="mr-3" />Đăng kí tài
            khoản
          </mdb-list-group-item>
        </router-link>
        <router-link to="/404" @click.native="activeItem = 13">
          <mdb-list-group-item :action="true" :class="activeItem === 13 && 'active'">
            <mdb-icon icon="exclamation" class="mr-3" />404
          </mdb-list-group-item>
        </router-link>
      </mdb-list-group>
    </div>
    <!-- /Sidebar  -->
    <main>
      <div class="p-5" style="padding-top: 0vmin!important">
        <router-view></router-view>
      </div>
      <ftr color="primary-color-dark" class="text-center font-small darken-2"></ftr>
    </main>
  </div>
</template>

<script>
import {
  mdbNavbar,
  mdbNavbarBrand,
  mdbNavItem,
  mdbNavbarNav,
  mdbNavbarToggler,
  mdbBtn,
  mdbIcon,
  mdbListGroup,
  mdbListGroupItem,
  mdbFooter,
  mdbBadge,
  waves,
} from "mdbvue";
import {
  mdbDropdown,
  mdbDropdownItem,
  mdbDropdownMenu,
  mdbDropdownToggle,
} from "mdbvue";
import Echo from "laravel-echo";
window.Pusher = require("pusher-js");

export default {
  name: "AdminTemplate",
  components: {
    mdbNavbar,
    mdbNavbarBrand,
    mdbNavItem,
    mdbNavbarNav,
    mdbNavbarToggler,
    mdbBtn,
    mdbListGroup,
    mdbListGroupItem,
    mdbIcon,
    ftr: mdbFooter,
    mdbDropdown,
    mdbDropdownItem,
    mdbDropdownMenu,
    mdbDropdownToggle,
    mdbBadge,
  },
  data() {
    return {
      activeItem: 1,
      isCustomer: false,
      pagination: {
        data: [],
        per_page: 10,
        current_page: 1,
        last_page: 1,
      },
      dataNotify: {
        data: null,
        id: 0,
        readAt: null,
      },
    };
  },
  computed: {
    permission() {
      return this.$store.state.user.authUser.permission;
    },
    notify() {
      console.log("store", this.$store.state.debt.notify);
      return this.$store.state.debt.notify;
    },
    countNotify() {
      console.log("store", this.$store.state.debt.notify);
      let count = 0;
      this.$store.state.debt.notify.forEach(u => {
        if (u.readAt == null) {
          count++;
        }
      });
      return count;
    },
  },
  created() {
    this.reload();
    window.Echo = new Echo({
      broadcaster: "pusher",
      key: process.env.MIX_PUSHER_APP_KEY,
      cluster: process.env.MIX_PUSHER_APP_CLUSTER,
      encrypted: true,
      forceTLS: true,
      auth: {
        headers: {
          Authorization: "Bearer " + this.$store.state.user.access_token,
        },
      },
    });

    window.Echo.private(
      "App.User." + this.$store.state.user.authUser.id
    ).notification((notification) => {
      if (notification.debtType == "created") {
        this.$toast.open({
          message:
            "Bạn đã nhận một nhắc nợ từ STK: " +
            notification.account.accountNumber,
          type: "info",
        });
      }

      if (notification.debtType == "paid") {
        this.$toast.open({
          message:
            "STK: " +
            notification.account.accountNumber +
            " đã thanh toán nhắc nợ",
          type: "success",
        });
      }

      if (notification.debtType == "deleted") {
        console.log("Da xoa");
        this.$toast.open({
          message:
            "STK: " + notification.account.accountNumber + " đã xóa nhắc nợ",
          type: "error",
        });
      }
      this.dataNotify.data = notification;
      this.dataNotify.id = notification.id;
      this.dataNotify.readAt = null;

      this.$store.dispatch("addNotify", this.dataNotify);
      console.log(notification, "#notifications");
    });
  },
  methods: {
    viewDebtDetail(t, r) {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      axios
        .post("api/bank/users/me/notify/" + t, {}, options)
        .then((response) => {
          if (response.status == 200) {
            console.log("gggg")
            this.$store.dispatch("setRead", t);
            this.$router.push({ name: "DebtDetail", params: { id: r } });
          }
        })
        .catch((error) => {
          return this.$toast.open({
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
        params: {
          status: this.filter,
        },
      };
      axios
        .get(
          "api/bank/users/me/notify?limit=" +
            this.pagination.per_page +
            "&page=" +
            this.pagination.current_page,
          options
        )
        .then((response) => {
          console.log("thông báo: ", response);
          if (response.data !== null) {
            this.pagination = response.data;
            this.$store.dispatch("setNotify", response.data.data);
          }
        })
        .catch((error) => {});
    },
    redirectProfile() {
      if (this.$router.history.current.name != "Profile") {
        this.$router.push({ name: "Profile" });
      }
    },
    logout() {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      axios
        .post("api/auth/logout", {}, options)
        .then((response) => {
          this.$toast.open({
            message: "Đăng xuất thành công",
            type: "success",
          });
          localStorage.removeItem(this.$store.state.user.authUser);
          localStorage.removeItem(this.$store.state.user.access_token);
          this.$router.push({ name: "Login" });
          console.log(response);
        })
        .catch((error) => {
          return this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error",
          });
        });
    },
    readAll() {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      axios
        .post("api/bank/users/me/notify", {}, options)
        .then((response) => {
          console.log("doc het", response);
          if (response.status == 200) {
            this.reload();
          }
        })
        .catch((error) => {
          return this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error",
          });
        });
    },
  },
  mixins: [waves],
};
</script>

<style>
@import url("https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap");
.navbar-light .navbar-brand {
  margin-left: 15px;
  color: #2196f3 !important;
  font-weight: bolder;
}
</style>

<style scoped>
.badge {
  background: red;
  position: absolute;
  top: 0;
  right: 0;
  border-radius: 0.5vmin;
}
.background-colum {
  background: #bbdefb;
}
.notify {
  position: absolute !important;
  min-width: 70vmin !important;
  max-width: 70vmin !important;
  height: 70vmin;
  overflow: auto;
}
.edit-dropdown {
  max-height: 40px;
}
main {
  background-color: #ededee;
}

.flexible-content {
  transition: padding-left 0.3s;
  padding-left: 270px;
}

.flexible-navbar {
  transition: padding-left 0.5s;
  padding-left: 270px;
}

.sidebar-fixed {
  left: 0;
  top: 0;
  height: 100vh;
  width: 300px;
  box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
  z-index: 1050;
  background-color: #fff;
  padding: 1.5rem;
  padding-top: 0;
}

.sidebar-fixed .logo-wrapper img {
  width: 100%;
  padding: 2.5rem;
}

.sidebar-fixed .list-group-item {
  display: block !important;
  transition: background-color 0.3s;
}

.sidebar-fixed .list-group .active {
  box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
  border-radius: 5px;
}

@media (max-width: 1199.98px) {
  .sidebar-fixed {
    display: none;
  }
  .flexible-content {
    padding-left: 0;
  }
  .flexible-navbar {
    padding-left: 10px;
  }
}
</style>
