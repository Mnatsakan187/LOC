// MonthlyIncome.js
import { Doughnut, mixins } from 'vue-chartjs'

export default {
  extends: Doughnut,
  mixins: [mixins.reactiveProp],
  props: ['chartData', 'options'],
  data () {
    return {
      selectedData: {}
    }
  },
  mounted () {
    this.renderChart(this.chartData, this.options)
  },
}




