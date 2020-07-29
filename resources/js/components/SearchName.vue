<template>
  <div
    class="modal fade show"
    id="modalLoginForm"
    tabindex="-1"
    style="display:block"
    role="dialog"
    aria-labelledby="myModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title w-100 font-weight-bold">Danh bạ người nhận</h4>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            @click="closeModal"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body mx-3">
          <autocomplete
            :search="search"
            placeholder="Nhập tên người nhận"
            aria-label="Search for a country"
            @submit="onSubmit"
          ></autocomplete>
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button class="btn btn-default" type="button" @click="checkCustomerInfo()">Xác nhận</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Autocomplete from "@trevoreyre/autocomplete-vue";
export default {
  props: {
    title: {
      tyle: String,
      default: ""
    },
    type: {
      tyle: String,
      default: "success" //danger,info,warning
    },
    message: {
      tyle: String,
      default: ""
    }
  },
  components: {
    Autocomplete
  },
  data() {
    return {
      isShowing: false,
      isShowingAfterTransfers: true,
      countries: ["aaaa", "bbbb", "aaa1"],
      users: [],
      reminder: [],
      lastPage: 0,
      accountNumber: 0
    };
  },
  created() {
    this.getReminderList();
  },
  methods: {
    closeModal() {
      this.$emit("close-modal", false);
    },
    getReminderList(page, limit) {
      const options = {
        headers: {
          Authorization: "bearer" + this.$store.state.user.access_token
        }
      };

      axios
        .post("api/remembers/search", {}, options)
        .then(response => {
          console.log("RESPONSE RECEIVED: ", response);

          this.reminder = response.data.data;
          for (const idx in response.data.data) {
            this.users.push(response.data.data[idx].name);
          }
          this.lastPage = response.data.last_page;
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.$store.dispatch("logOut");
            return;
          }
        });
    },
    search(input) {
      if (input.length < 1) {
        return [];
      }
      return this.users.filter(country => {
        return country.toLowerCase().startsWith(input.toLowerCase());
      });
    },
    onSubmit(result) {
      const idx = this.reminder.findIndex(u => u.name === result);
      if (idx >= 0) {
        this.accountNumber = this.reminder[idx].accountId;
        this.$emit("user-reminder", this.accountNumber);
      }   
    },
    checkCustomerInfo() {
      this.$store.dispatch("setAccountNumber", this.accountNumber);
      this.closeModal();
    }
  }
};
</script>
<style scoped>
button:focus {
  outline: 0;
}
</style>