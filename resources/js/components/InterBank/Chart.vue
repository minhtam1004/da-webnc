<template>
  <section id="tables">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <!-- <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Danh sách khách hàng</h4>
          </mdb-view>-->
          <mdb-card-body>
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
                      <mdb-icon icon="chart-bar" class="red accent-2" />
                      <div class="data">
                        <p>Tổng số tiền nhận</p>
                        <h4>
                          <strong>{{ total - totalTransfer }}</strong>
                        </h4>
                      </div>
                    </div>
                    <mdb-card-body></mdb-card-body>
                  </mdb-card>
                </mdb-col>
              </mdb-row>
            </section>
            <mdb-container>
              <mdb-bar-chart
                v-if="isLoad"
                :data="barChartData"
                :options="barChartOptions"
                :height="300"
                :width="900"
              ></mdb-bar-chart>
            </mdb-container>
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>

<script>
import { mdbBarChart, mdbContainer } from "mdbvue";
import {
  mdbRow,
  mdbCol,
  mdbCard,
  mdbView,
  mdbCardBody,
  mdbTbl,
  mdbIcon,
} from "mdbvue";
export default {
  name: "ChartPage",
  components: {
    mdbBarChart,
    mdbContainer,
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl,
    mdbIcon,
  },
  data() {
    return {
      total: 0,
      totalTransfer: 0,
      isLoad: false,
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
        responsive: false,
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
            },
          ],
        },
      },
    };
  },
  computed: {
    dataChart() {
      return this.$store.state.chart.data;
    },
  },
  watch: {
    dataChart() {
      console.log("mimi", this.dataChart);
      // this.dataChart.data.forEach((u) => {
      //   this.total += u.amount;
      //   console.log("t", this.formatTime(u.created_at));
      //   this.barChartData.labels.push(this.formatTime(u.created_at));
      //   if (!u.sendBank) {
      //     this.totalTransfer += u.amount;
      //     this.barChartData.datasets[0].data.push(u.amount);
      //   }
      //   if (!u.receivedBank) {
      //     this.barChartData.datasets[1].data.push(u.amount);
      //   }
      //   console.log("p", this.barChartData.labels);
      // });

      if (this.dataChart.data.length > 0) {
        // this.pagination.data.forEach((u) => {
        //   this.total += u.amount;
        // });

        this.dataChart.data.forEach((u) => {
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
    },
  },
  created() {},

  methods: {
    formatTime(time) {
      const a = new Date(time);
      if (a.getMonth() < 10) {
        return a.getDate() + "/0" + a.getMonth() + "/" + a.getFullYear();
      }
      return a.getDate() + "/" + a.getMonth() + "/" + a.getFullYear();
    },
  },
};
</script>

<style scoped>
.user-select {
  user-select: none;
}
.card.card-cascade .view.gradient-card-header {
  padding: 1rem 1rem;
  text-align: center;
}
.card.card-cascade h3,
.card.card-cascade h4 {
  margin-bottom: 0;
}
.pt-3-half {
  padding-top: 1.4rem;
}

label {
  margin-left: 20px;
}
#datepicker {
  width: 20vmin;
  margin: 0 20px 20px 20px;
}
#datepicker > span:hover {
  cursor: pointer;
}

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
