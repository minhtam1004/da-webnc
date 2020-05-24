<template>
  <v-form>
    <v-container>
      <v-row v-if="isShowingAfterTransfers">
        <v-col cols="12" sm="6">
          <v-text-field label="SỐ TÀI KHOẢN" outlined></v-text-field>
        </v-col>
        <v-col cols="12" sm="6">
          <v-btn class="ma-1" large color="success" @click.stop="isShowing = true">
            <v-icon left>mdi-account-check</v-icon>KIỂM TRA SỐ TÀI KHOẢN
          </v-btn>
        </v-col>

        <v-col cols="12" sm="12" v-if="isShowing">
          <v-text-field label="TÊN CHỦ TÀI KHOẢN" outlined disable></v-text-field>
        </v-col>

        <v-col cols="12" sm="12" v-if="isShowing">
          <v-select :items="items" label="CHỌN NGÂN HÀNG" outlined></v-select>
        </v-col>

        <v-col cols="12" sm="12" v-if="isShowing">
          <v-text-field label="NHẬP SỐ TIỀN" outlined></v-text-field>
        </v-col>

        <v-col cols="12" sm="6" v-if="isShowing" class="text-right">
          <div class="my-2">
            <v-btn color="primary" dark large @click.stop="isShowingAfterTransfers = false">Xác nhận</v-btn>
          </div>
        </v-col>
        <v-col cols="12" sm="6" v-if="isShowing">
          <div class="my-2">
            <v-btn color="primary" dark large outlined>Đặt lại</v-btn>
          </div>
        </v-col>
      </v-row>

      <v-row v-if="!isShowingAfterTransfers">
        <v-col cols="12" sm="12">
          <v-text-field label="NHẬP MẬT KHẨU CHUYỂN TIỀN" outlined></v-text-field>
        </v-col>

        <v-col cols="12" sm="12" class="text-center">
          <div class="my-2">
            <v-btn color="primary" dark large>CHUYỂN TIỀN</v-btn>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
</template>
<script>
export default {
  data() {
    return {
      isShowing: false,
      items: ["Group 5 banks", "Group 34 banks"],
      isShowingAfterTransfers: true
    };
  }
};
</script>