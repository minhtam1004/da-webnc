<template>
    <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="7" md="7">
                <v-card class="elevation-12">
                    <v-toolbar
                        style="justify-content: center!important"
                        color="primary"
                        dark
                        src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
                        prominent
                        flat
                    >
                        <v-toolbar-title>Đăng kí</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                label="Tên đăng nhập"
                                append-icon="mdi-account"
                                type="text"
                                outlined
                            ></v-text-field>

                            <v-text-field
                                v-model="password"
                                :append-icon="
                                    isShowingPassword
                                        ? 'mdi-eye'
                                        : 'mdi-eye-off'
                                "
                                :rules="[rules.required, rules.min]"
                                :type="isShowingPassword ? 'text' : 'password'"
                                outlined
                                label="Mật khẩu"
                                @click:append="
                                    isShowingPassword = !isShowingPassword
                                "
                            ></v-text-field>

                            <v-text-field
                                label="Họ và tên"
                                v-model="name"
                                append-icon="mdi-rename-box"
                                type="text"
                                outlined
                            ></v-text-field>

                            <v-text-field
                                v-model="email"
                                label="Địa chỉ email"
                                append-icon="mdi-email"
                                type="text"
                                outlined
                            ></v-text-field>

                            <v-text-field
                                v-model="phone"
                                label="Số điện thoại"
                                append-icon="mdi-phone"
                                type="text"
                                outlined
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions style="justify-content: center;">
                        <v-row>
                            <v-col md="6" sm="6" cols="12" class="text-right">
                                <v-btn color="primary" outlined>Hủy bỏ</v-btn>
                            </v-col>
                            <v-col md="6" sm="6" cols="12">
                                <v-btn color="primary">Tạo tài khoản</v-btn>
                            </v-col>
                        </v-row>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            isShowingPassword: false,
            password: "",
            name: "",
            email: "",
            phone: "",
            rules: {
                required: value => !!value || "Mật khẩu không được bỏ trống",
                min: v =>
                    (v.length < 6 && v.length > 32) ||
                    "Mật khẩu từ 6 đến 32 kí tự"
            }
        };
    }
};
</script>
