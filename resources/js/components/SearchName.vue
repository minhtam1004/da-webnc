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
          >
            <template #result="{ result, props }">
              <li v-bind="props">
                <div class="wiki-title">
                  {{ result }}
                  <button
                    style="margin-left:28vmin!important"
                    type="button"
                    class="btn btn-primary px-4"
                    @click="update(result)"
                  >
                    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                  </button>
                  <button
                    style="margin-left:1vmin!important"
                    type="button"
                    class="btn btn-danger px-4"
                  >
                    <i class="fas fa-times-circle" aria-hidden="true"></i>
                  </button>
                </div>
              </li>
            </template>
          </autocomplete>
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button class="btn btn-default" type="button" @click="checkCustomerInfo()">Xác nhận</button>
        </div>
        <UpdateReminder :name="nameReminder" :id="idReminder" v-if="showAddReminder" @close-modal="showAddReminder = false" />
      </div>
    </div>
  </div>
</template>
<script>
import Autocomplete from "@trevoreyre/autocomplete-vue";
import UpdateReminder from "./Popup/UpdateReminder";
export default {
  props: {
    title: {
      tyle: String,
      default: "",
    },
    type: {
      tyle: String,
      default: "success", //danger,info,warning
    },
    message: {
      tyle: String,
      default: "",
    }
  },
  components: {
    Autocomplete,
    UpdateReminder,
  },
  data() {
    return {
      isShowing: false,
      nameReminder: "",
      idReminder: 0,
      showAddReminder: false,
      isShowingAfterTransfers: true,
      countries: ["aaaa", "bbbb", "aaa1"],
      users: [],
      reminder: [],
      lastPage: 0,
      accountNumber: 0,
      bankId: null,
      data: [],
    };
  },
  created() {
    this.getReminderList();
  },
  methods: {
    closeModal() {
      this.$emit("close-modal", false);
    },
    update(t) {
      const index = this.reminder.findIndex((u) => u.name == t);
      if (index >= 0) {
        this.showAddReminder = true;
        this.nameReminder = this.reminder[index].name;
        this.idReminder = this.reminder[index].id;
      }
    },
    getReminderList(page, limit) {
      const options = {
        headers: {
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };

      axios
        .post("api/remembers/search", {}, options)
        .then((response) => {
          console.log("RESPONSE RECEIVED: ", response);

          this.reminder = response.data.data;
          for (const idx in response.data.data) {
            this.users.push(response.data.data[idx].name);
          }
          this.lastPage = response.data.last_page;
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.$store.dispatch("logOut");
            return;
          }
        });
    },
    search(input) {
      if (input.length < 1) {
        return this.users;
      }
      return this.users.filter((country) => {
        return country.toLowerCase().startsWith(input.toLowerCase());
      });
    },
    onSubmit(result) {
      const idx = this.reminder.findIndex((u) => u.name === result);
      if (idx >= 0) {
        this.accountNumber = this.reminder[idx].accountId;
        this.bankId = this.reminder[idx].bankId;
      }
    },
    checkCustomerInfo() {
      this.$store.dispatch("setAccountNumber", this.accountNumber);
      console.log("555", this.accountNumber);
      this.$emit("user-reminder", this.accountNumber, this.bankId);
      this.closeModal();
    },
  },
};
</script>
<style scoped>
button:focus {
  outline: 0;
}
</style>