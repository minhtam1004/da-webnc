<template>
  <div>
    <div
      class="modal fade show"
      style="display:block"
      id="centralModalSuccess"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myModalLabel"
      aria-hidden="true"
    >
      <div :class="'modal-dialog modal-notify modal-' + type" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Header-->
          <div class="modal-header">
            <p class="heading lead">{{title}}</p>

            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="closeModal"
            >
              <span aria-hidden="true" class="white-text">&times;</span>
            </button>
          </div>

          <!--Body-->
          <div class="modal-body">
            <div class="text-center">
              <i v-if="type === 'success'" class="fas fa-check fa-4x mb-3 animated rotateIn"></i>
              <i v-else-if="type=== 'danger'" class="fas fa-times fa-4x mb-3 animated rotateIn"></i>
              <i v-else-if="type=== 'warning'" class="fas fa-exclamation-triangle fa-4x mb-3 animated rotateIn"></i>
              <i v-else class="fas fa-info fa-4x mb-3 animated rotateIn"></i>
              <p>{{message}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    width: {
      style: String,
      default: "120vmin"
    },
    height: {
      tyle: String,
      default: "0vmin"
    },
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
    },
    paddingY: {
      style: String,
      default: "0vmin"
    }
  },
  data() {
    return {
      isShowing: false,
      items: ["Group 5 banks", "Group 34 banks"],
      isShowingAfterTransfers: true
    };
  },
  methods: {
    closeModal() {
      this.$emit("close-modal", false);
    }
  }
};
</script>
<style scoped>
button:focus {
  outline: 0;
}
</style>