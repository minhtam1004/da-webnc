<template>
  <section id="tables">
    <mdb-row>
      <mdb-col md="12">
        <mdb-card cascade narrow class="mt-5">
          <mdb-view class="gradient-card-header blue darken-2">
            <h4 class="h4-responsive text-white">Danh sách khách hàng</h4>
          </mdb-view>
          <mdb-card-body>
            <div id="table" class="table-editable">
              <span class="table-add float-right mb-3 mr-2">
                <a href="#!" class="text-success">
                  <i class="fas fa-plus fa-2x" aria-hidden="true"></i>
                </a>
              </span>
              <table class="table table-bordered table-responsive-md table-striped text-center">
                <thead>
                  <tr>
                    <th class="text-center">Mã giao dịch</th>
                    <th class="text-center">Người gửi</th>
                    <th class="text-center">Người nhận</th>
                    <th class="text-center">Số tiền</th>
                    <th class="text-center">Phí</th>
                    <th class="text-center">Thời gian giao dịch</th>
                    <th class="text-center">Trạng thái</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in 4" :key="index">
                    <td class="pt-3-half" contenteditable="true">Aurelia Vega</td>
                    <td class="pt-3-half" contenteditable="true">30</td>
                    <td class="pt-3-half" contenteditable="true">Deepends</td>
                    <td class="pt-3-half" contenteditable="true">Spain</td>
                    <td class="pt-3-half" contenteditable="true">Madrid</td>
                    <td class="pt-3-half">
                      <span class="table-up">
                        <a href="#!" class="indigo-text">
                          <i class="fas fa-long-arrow-alt-up" aria-hidden="true"></i>
                        </a>
                      </span>
                      <span class="table-down">
                        <a href="#!" class="indigo-text">
                          <i class="fas fa-long-arrow-alt-down" aria-hidden="true"></i>
                        </a>
                      </span>
                    </td>
                    <td>
                      <span class="table-remove">
                        <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">Remove</button>
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- Editable table -->
          </mdb-card-body>
        </mdb-card>
      </mdb-col>
    </mdb-row>
  </section>
</template>

<script>
import { mdbRow, mdbCol, mdbCard, mdbView, mdbCardBody, mdbTbl } from "mdbvue";

export default {
  name: "Tables",
  components: {
    mdbRow,
    mdbCol,
    mdbCard,
    mdbView,
    mdbCardBody,
    mdbTbl
  },

  data() {
    return {
      data: []
    };
  },
  created() {
    const $tableID = $("#table");
    const $BTN = $("#export-btn");
    const $EXPORT = $("#export");

    const newTr = `
<tr class="hide">
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half" contenteditable="true">Example</td>
  <td class="pt-3-half">
    <span class="table-up"><a href="#!" class="indigo-text"><i class="fas fa-long-arrow-alt-up" aria-hidden="true"></i></a></span>
    <span class="table-down"><a href="#!" class="indigo-text"><i class="fas fa-long-arrow-alt-down" aria-hidden="true"></i></a></span>
  </td>
  <td>
    <span class="table-remove"><button type="button" class="btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light">Remove</button></span>
  </td>
</tr>`;

    $(".table-add").on("click", "i", () => {
      const $clone = $tableID
        .find("tbody tr")
        .last()
        .clone(true)
        .removeClass("hide table-line");

      if ($tableID.find("tbody tr").length === 0) {
        $("tbody").append(newTr);
      }

      $tableID.find("table").append($clone);
    });

    $tableID.on("click", ".table-remove", function() {
      $(this)
        .parents("tr")
        .detach();
    });

    $tableID.on("click", ".table-up", function() {
      const $row = $(this).parents("tr");

      if ($row.index() === 0) {
        return;
      }

      $row.prev().before($row.get(0));
    });

    $tableID.on("click", ".table-down", function() {
      const $row = $(this).parents("tr");
      $row.next().after($row.get(0));
    });

    // A few jQuery helpers for exporting only
    jQuery.fn.pop = [].pop;
    jQuery.fn.shift = [].shift;

    $BTN.on("click", () => {
      const $rows = $tableID.find("tr:not(:hidden)");
      const headers = [];
      const data = [];

      // Get the headers (add special header logic here)
      $($rows.shift())
        .find("th:not(:empty)")
        .each(function() {
          headers.push(
            $(this)
              .text()
              .toLowerCase()
          );
        });

      // Turn all existing rows into a loopable array
      $rows.each(function() {
        const $td = $(this).find("td");
        const h = {};

        // Use the headers from earlier to name our hash keys
        headers.forEach((header, i) => {
          h[header] = $td.eq(i).text();
        });

        data.push(h);
      });

      // Output the result
      $EXPORT.text(JSON.stringify(data));
    });
  },
  methods: {}
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
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
</style>
