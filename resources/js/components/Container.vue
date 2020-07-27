<template>
  <div class="flexible-content">
    <!--Navbar-->
    <mdb-navbar class="flexible-navbar white" light position="top" scrolling>
      <mdb-navbar-brand target="_blank" style="margin-left: 7vmin;">KLXBank</mdb-navbar-brand>
      <mdb-navbar-toggler>
        <mdb-navbar-nav left>
          <mdb-nav-item to="/dashboard" waves-fixed active class="active">Dashborad</mdb-nav-item>
          <mdb-nav-item waves-fixed to="/profile">Thông tin cá nhân</mdb-nav-item>
          <mdb-nav-item to="/account-info" waves-fixed>Thông tin tài khoản</mdb-nav-item>
          <mdb-nav-item to="/transactions" waves-fixed>Lịch sử giao dịch</mdb-nav-item>
        </mdb-navbar-nav>
        <mdb-navbar-nav right>
          <mdb-btn tag="a" gradient="blue" floating size="sm" @click="logout()">
            <mdb-icon icon="sign-out-alt" />
          </mdb-btn>
        </mdb-navbar-nav>
      </mdb-navbar-toggler>
    </mdb-navbar>
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
            <mdb-icon icon="dollar-sign" class="mr-3" />Chuyển khoản nội bộ
          </mdb-list-group-item>
        </router-link>

        <router-link to="/transfers-interbank" @click.native="activeItem = 5" v-if="permission == 'user'">
          <mdb-list-group-item :action="true" :class="activeItem === 5 && 'active'">
            <mdb-icon icon="university" class="mr-3" />Chuyển khoản liên ngân hàng
          </mdb-list-group-item>
        </router-link>

        <router-link to="/add-debt-reminder" @click.native="activeItem = 6" v-if="permission == 'user'">
          <mdb-list-group-item :action="true" :class="activeItem === 6 && 'active'">
            <mdb-icon icon="university" class="mr-3" />Tạo nhắc nợ
          </mdb-list-group-item>
        </router-link>

        <router-link to="/employees" @click.native="activeItem = 7" v-if="permission == 'admin'">
          <mdb-list-group-item :action="true" :class="activeItem === 7 && 'active'">
            <mdb-icon icon="user-secret" class="mr-3" />Quản lí nhân viên
          </mdb-list-group-item>
        </router-link>

        <router-link to="/customers" @click.native="activeItem = 8" v-if="permission == 'editor'">
          <mdb-list-group-item :action="true" :class="activeItem === 8 && 'active'">
            <mdb-icon icon="user-secret" class="mr-3" />Quản lí khách hàng
          </mdb-list-group-item>
        </router-link>

        <router-link to="/transactions" @click.native="activeItem = 9" v-if="permission == 'user'">
          <mdb-list-group-item :action="true" :class="activeItem === 9 && 'active'">
            <mdb-icon icon="hand-holding-usd" class="mr-3" />Danh sách giao dịch
          </mdb-list-group-item>
        </router-link>

        <router-link to="/banks" @click.native="activeItem = 10" v-if="permission == 'admin'">
          <mdb-list-group-item :action="true" :class="activeItem === 10 && 'active'">
            <mdb-icon icon="hand-holding-usd" class="mr-3" />Danh sách ngân hàng liên kết
          </mdb-list-group-item>
        </router-link>

          <router-link to="/debt-remider" @click.native="activeItem = 11" v-if="permission == 'user'">
          <mdb-list-group-item :action="true" :class="activeItem === 11 && 'active'">
            <mdb-icon icon="hand-holding-usd" class="mr-3" />Danh sách nhắc nợ
          </mdb-list-group-item>
        </router-link>
        <!-- <router-link to="/maps" @click.native="activeItem = 6">
          <mdb-list-group-item :action="true" :class="activeItem === 6 && 'active'">
            <mdb-icon icon="map" class="mr-3" />Maps
          </mdb-list-group-item>
        </router-link>-->
        <router-link to="/register-customer" @click.native="activeItem = 12" v-if="permission == 'editor'">
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
      <div class="p-5">
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
  waves
} from "mdbvue";
import {
  mdbDropdown,
  mdbDropdownItem,
  mdbDropdownMenu,
  mdbDropdownToggle
} from "mdbvue";
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
    mdbDropdownToggle
  },
  data() {
    return {
      activeItem: 1,
      isCustomer: false
    };
  },
  computed: {
    permission() {
      return this.$store.state.user.authUser.permission;
    }
  },
  created() {
    console.log("++++++", this.$store.state.user.authUser);
    console.log('asd');
    
    Echo.private("App.User." + this.$store.state.user.authUser.id).notification(
      (notification) => {
        console.log(notification, "#notifications");
      }
    );

  },
  methods: {
    logout() {
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };
      axios
        .post("api/auth/logout", {}, options)
        .then(response => {
          this.$toast.open({
            message: "Đăng xuất thành công",
            type: "success"
          });
          localStorage.removeItem(this.$store.state.user.authUser);
          localStorage.removeItem(this.$store.state.user.access_token);
          this.$router.push({ name: "Login" });
          console.log(response);
        })
        .catch(error => {
          return this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "error"
          });
        });
    }
  },
  mixins: [waves]
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
