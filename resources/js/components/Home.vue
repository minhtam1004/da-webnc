<template>
  <v-card class="mx-auto" width="100%">
    <v-img
      class="white--text align-end"
      height="200px"
      src="https://cdn.vuetifyjs.com/images/cards/docks.jpg"
    >
      <v-card-title>
        <v-col>
          <v-row>
            <v-avatar size="100px">
              <img src="https://cdn.vuetifyjs.com/images/john.jpg" alt="John" />
            </v-avatar>
          </v-row>
          <v-row>Your name</v-row>
        </v-col>
      </v-card-title>
    </v-img>

	<v-card-text class="text--primary">
   
 
    <v-tabs v-model="tab" background-color="deep-purple accent-4" dark icons-and-text>
      <v-tabs-slider></v-tabs-slider>

      <v-tab href="#tab-1">
        Profile
        <v-icon>mdi-account-box</v-icon>
      </v-tab>

      <v-tab href="#tab-2">
        Transfers
        <v-icon>mdi-bank-transfer</v-icon>
      </v-tab>

      <v-tab href="#tab-3">
        Transaction History
        <v-icon>mdi-handshake</v-icon>
      </v-tab>

      <v-tab href="#tab-4">
        Debt Reminder
        <v-icon>mdi-power-socket-us</v-icon>
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">


      <v-tab-item value="tab-1">
        <v-card flat>
          <v-card-text>
            <Profile />
          </v-card-text>
        </v-card>
      </v-tab-item>

       <v-tab-item value="tab-2">
        <v-card flat>
          <v-card-text>
            <Transfers />
          </v-card-text>
        </v-card>
      </v-tab-item>

      <v-tab-item value="tab-3">
        <v-card flat>
          <v-card-text>
            <TransactionHistory />
          </v-card-text>
        </v-card>
      </v-tab-item>

    </v-tabs-items>

	 
	</v-card-text>
 </v-card>
</template>
<script>
import Profile from "./Profile"
import Transfers from "./Transfers"
import TransactionHistory from "./TransactionHistory"
export default {
  components: {
    Profile,
    Transfers,
    TransactionHistory
  },
  data() {
    return {
      tab: null,
      text:
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
    };
  }
};
</script>