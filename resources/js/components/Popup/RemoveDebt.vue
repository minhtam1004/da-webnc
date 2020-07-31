<template>
  <Popup @close-modal="closeAll" title="Xác nhận xóa nhắc nợ" minWidth="130vmin">
    <section id="profile">
      <mdb-row>
        <mdb-col md="12">
          <mdb-card cascade narrow>
            <mdb-card-body style="height:60vh;overflow: auto">
              <form data-toggle="validator" role="form" id="register-form">
                <label for="reason" data-error data-success>Nhập nội dung</label>
                <textarea class="form-control rounded-0" v-model="reason" rows="3"></textarea>
                <br />

                <div class="text-center mt-4">
                  <button
                    class="btn btn-deep-orange"
                    type="button"
                    :disabled="loading"
                    id="btn-one"
                    @click="deleteDebt"
                  >
                    <i v-if="loading" class="fa fa-spinner fa-spin"></i>Xác nhận
                  </button>
                </div>
              </form>
            </mdb-card-body>
          </mdb-card>
        </mdb-col>
      </mdb-row>
    </section>
  </Popup>
</template>
<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";
import Modal from "./../Modal";
import $ from "jquery";
import validate from "jquery-validation";
import Popup from "./Popup";
export default {
  name: "Profile",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    Popup,
    Modal,
  },
  props: {
    idDebt: {
      tyle: String,
      default: ""
    }
  },
  data() {
    return {
      id: this.$route.params.id,
      loading: false,
      reason: "",
    };
  },
  methods: {
    closeAll() {
      this.$emit("close-modal");
    },
    turnOnLoading() {
      $("#btn-one")
        .html(
          '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Xác nhận'
        )
        .addClass("disabled");
    },
    turnOffLoading() {
      $("#btn-one").removeClass("disabled");
      $("#btn-one span").remove();
    },
    deleteDebt() {
      this.turnOnLoading();
      this.loading = true;
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      axios
        .delete("api/debt/" + this.idDebt, options)
        .then((response) => {
          if (response.status == 200) {
          this.turnOffLoading();
          this.loading = false;
          this.$toast.open({
            message: "Nhắc nợ đã được xóa",
            type: "success",
          });
          this.$emit("close-modal");
          this.$router.push({ name: 'DebtReminder'});
          }
        })
        .catch((error) => {
          this.turnOffLoading();
          this.loading = false;
          this.$toast.open({
            message: "Có lỗi xảy ra",
            type: "danger",
          });
        });
    }
  },
};
</script>

<style scoped>
button:focus {
  outline: 0;
}
#timer {
  font-size: 2.5vmin;
  color: blue;
}
#buttons {
  display: flex;
}
button {
  margin: 2px;
}
.profile-card-footer {
  background-color: #f7f7f7 !important;
  padding: 1.25rem;
}
.card.card-cascade .view {
  box-shadow: 0 3px 10px 0 rgba(0, 0, 0, 0.15), 0 3px 12px 0 rgba(0, 0, 0, 0.15);
}
.card.card-cascade .view.gradient-card-header {
  padding: 1rem 1rem;
  text-align: center;
}
.card.card-cascade h3,
.card.card-cascade h4 {
  margin-bottom: 0;
}
</style>
