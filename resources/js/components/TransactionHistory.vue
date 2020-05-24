<template>
  <v-data-table
    :headers="headers"
    :items="desserts"
    :items-per-page="5"
    class="elevation-1"
  ></v-data-table>
</template>
<script>
  export default {
    data () {
      return {
        headers: [
          {
            text: 'Thời gian giao dịch',
            align: 'start',
            sortable: false,
            value: 'createdAt',
          },
          { text: 'Số tiền', value: 'money' },
          { text: 'Số dư', value: 'excess' },
        ],
        desserts: [
          {
            createdAt: '10-10-2020',
            money: 159000,
            excess: 5000000,
          },
          {
            createdAt: '10-10-2020',
            money: 159000,
            excess: 5000000,
          },
          {
            createdAt: '10-10-2020',
            money: 159000,
            excess: 5000000,
          },
          {
            createdAt: '10-10-2020',
            money: 159000,
            excess: 5000000,
          },
          {
            createdAt: '10-10-2020',
            money: 159000,
            excess: 5000000,
          },
          {
            createdAt: '10-10-2020',
            money: 159000,
            excess: 5000000,
          },
          {
            createdAt: '10-10-2020',
            money: 159000,
            excess: 5000000,
          },
      
        ],
      }
    },
  }
</script>