<template>
  <section id="dashboard">
    <section class="mt-lg-5">
      <mdb-row>
        <mdb-col xl="4" md="6" class="mb-r">
          <mdb-card cascade class="cascading-admin-card">
            <div class="admin-up">
              <mdb-icon icon="money-bill-alt" far class="primary-color" />
              <div class="data">
                <p>Tổng số tiền</p>
                <h4>
                  <strong>{{ total }}</strong>
                </h4>
              </div>
            </div>
            <mdb-card-body></mdb-card-body>
          </mdb-card>
        </mdb-col>
        <mdb-col xl="4" md="6" class="mb-r">
          <mdb-card cascade class="cascading-admin-card">
            <div class="admin-up">
              <mdb-icon icon="chart-line" class="warning-color" />
              <div class="data">
                <p>Tổng số tiền chuyển</p>
                <h4>
                  <strong>{{ totalTransfer }}</strong>
                </h4>
              </div>
            </div>
            <mdb-card-body></mdb-card-body>
          </mdb-card>
        </mdb-col>
        <mdb-col xl="4" md="6" class="mb-r">
          <mdb-card cascade class="cascading-admin-card">
            <div class="admin-up">
              <mdb-icon icon="chart-pie" class="light-blue lighten-1" />
              <div class="data">
                <p>Tổng số tiền nhận</p>
                <h4>
                  <strong>{{ total - totalTransfer}}</strong>
                </h4>
              </div>
            </div>
            <mdb-card-body></mdb-card-body>
          </mdb-card>
        </mdb-col>
      </mdb-row>
    </section>
    <section>
      <mdb-row class="mt-5">
        <mdb-col md="12" class="mb-4">
          <mdb-card>
            <mdb-card-body>
              <div style="display: block">
                <mdb-bar-chart
                  v-if="isLoad"
                  :data="barChartData"
                  :options="barChartOptions"
                  :height="500"
                />
              </div>
            </mdb-card-body>
          </mdb-card>
        </mdb-col>
      </mdb-row>
    </section>
  </section>
</template>

<script>
import {
  mdbRow,
  mdbCol,
  mdbBtn,
  mdbCard,
  mdbCardBody,
  mdbCardHeader,
  mdbCardText,
  mdbIcon,
  mdbTbl,
  mdbBarChart,
  mdbPieChart,
  mdbLineChart,
  mdbRadarChart,
  mdbDoughnutChart,
  mdbListGroup,
  mdbListGroupItem,
  mdbBadge,
  mdbModal,
  mdbModalHeader,
  mdbModalTitle,
  mdbModalBody,
  mdbModalFooter,
} from "mdbvue";

export default {
  name: "Dashboard",
  components: {
    mdbRow,
    mdbCol,
    mdbBtn,
    mdbCard,
    mdbCardBody,
    mdbCardHeader,
    mdbCardText,
    mdbIcon,
    mdbTbl,
    mdbBarChart,
    mdbPieChart,
    mdbLineChart,
    mdbRadarChart,
    mdbDoughnutChart,
    mdbListGroup,
    mdbListGroupItem,
    mdbBadge,
    mdbModal,
    mdbModalHeader,
    mdbModalTitle,
    mdbModalBody,
    mdbModalFooter,
  },
  data() {
    return {
      keyword: "",
      isLoad: false,
      lastPage: 1,
      pagination: {
        data: [],
        per_page: 10,
        current_page: 1,
        last_page: 1,
      },
      total: 0,
      totalTransfer: 0,
      showFrameModalTop: false,
      showFrameModalBottom: false,
      showSideModalTopRight: false,
      showSideModalTopLeft: false,
      showSideModalBottomRight: false,
      showSideModalBottomLeft: false,
      showCentralModalSmall: false,
      showCentralModalMedium: false,
      showCentralModalLarge: false,
      showCentralModalFluid: false,
      showFluidModalRight: false,
      showFluidModalLeft: false,
      showFluidModalTop: false,
      showFluidModalBottom: false,
      barChartData: {
        labels: [],
        datasets: [
          {
            label: "Số tiền chuyển",
            data: [],
            backgroundColor: "rgba(245, 74, 85, 0.5)",
            borderWidth: 1,
          },
          {
            label: "Số tiền nhận",
            data: [],
            backgroundColor: "rgba(245, 192, 50, 0.5)",
            borderWidth: 1,
          },
        ],
      },
      barChartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          xAxes: [
            {
              barPercentage: 1,
              gridLines: {
                display: true,
                color: "rgba(0, 0, 0, 0.1)",
              },
            },
          ],
          yAxes: [
            {
              gridLines: {
                display: true,
                color: "rgba(0, 0, 0, 0.1)",
              },
              ticks: {
                beginAtZero: true,
              },
            },
          ],
        },
      },
    };
  },
  created() {
    this.reload();
  },
  watch: {
    lastPage() {
      if (this.lastPage >= 2) {
        for (let i = 2; i <= response.data.last_page; i++) {
          this.reload("", 10, i);
          this.lastPage--;
        }
      }
    },
  },
  methods: {
    formatTime(time) {
      const a = new Date(time);
      if (a.getMonth() < 10) {
        return a.getDate() + "/0" + a.getMonth() + "/" + a.getFullYear();
      }
      return a.getDate() + "/" + a.getMonth() + "/" + a.getFullYear();
    },
    reload(keyword = "", page = 1, limit = 10) {
      this.total = 0;
      const options = {
        headers: {
          "Content-Type": "application/json",
          Authorization: "bearer" + this.$store.state.user.access_token,
        },
      };
      console.log(this.startDate);
      console.log(this.endDate);
      axios
        .get(
          "api/banks/transfers?keyword=" +
            keyword +
            "&limit=" +
            limit +
            "&page=" +
            page,
          options
        )
        .then((response) => {
          console.log("RESPONSE RECEIVED: ", response);
          if (response.data !== null) {
            this.pagination = response.data;
            if (this.lastPage == 1 && response.data.last_page > 1) {
              this.lastPage = response.data.last_page;
            }
            if (this.pagination.data.length > 0) {
              // this.pagination.data.forEach((u) => {
              //   this.total += u.amount;
              // });

              this.pagination.data.forEach((u) => {
                this.total += u.amount;
                console.log("t", this.formatTime(u.created_at));
                const index = this.barChartData.labels.findIndex(
                  (x) => x == this.formatTime(u.created_at)
                );
                if (index < 0) {
                  this.barChartData.labels.push(this.formatTime(u.created_at));
                  const idx = this.barChartData.labels.findIndex(
                    (x) => x == this.formatTime(u.created_at)
                  );

                  if (!u.sendBank) {
                    this.totalTransfer += u.amount;
                    this.barChartData.datasets[0].data[idx] = u.amount;
                  }
                  if (!u.receivedBank) {
                    this.barChartData.datasets[1].data[idx] = u.amount;
                  }
                } else {
                  if (!u.sendBank) {
                    this.totalTransfer += u.amount;
                    this.barChartData.datasets[0].data[index] = u.amount;
                  }
                  if (!u.receivedBank) {
                    this.barChartData.datasets[1].data[index] = u.amount;
                  }
                }

                console.log("p", this.barChartData.labels);
              });
            }

            this.isLoad = true;
          }
        })
        .catch((error) => {});
    },
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.cascading-admin-card {
  margin: 20px 0;
}
.cascading-admin-card .admin-up {
  margin-left: 4%;
  margin-right: 4%;
  margin-top: -20px;
}
.cascading-admin-card .admin-up .fas,
.cascading-admin-card .admin-up .far {
  box-shadow: 0 2px 9px 0 rgba(0, 0, 0, 0.2), 0 2px 13px 0 rgba(0, 0, 0, 0.19);
  padding: 1.7rem;
  font-size: 2rem;
  color: #fff;
  text-align: left;
  margin-right: 1rem;
  border-radius: 3px;
}
.cascading-admin-card .admin-up .data {
  float: right;
  margin-top: 2rem;
  text-align: right;
}
.admin-up .data p {
  color: #999999;
  font-size: 12px;
}
.classic-admin-card .card-body {
  color: #fff;
  margin-bottom: 0;
  padding: 0.9rem;
}
.classic-admin-card .card-body p {
  font-size: 13px;
  opacity: 0.7;
  margin-bottom: 0;
}
.classic-admin-card .card-body h4 {
  margin-top: 10px;
}
</style>
