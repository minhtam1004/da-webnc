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
      <div :class="'modal-dialog modal-notify modal-' + type" role="document"
      :style="{minWidth: minWidth}"
>
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
          <slot></slot>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    title: {
      tyle: String,
      default: ""
    },
    minWidth: {
      type: String,
      default: "150vmin"
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
  data() {
    return {
      isShowing: false,
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