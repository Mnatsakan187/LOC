// MonthlyIncome.js
import { HorizontalBar, mixins } from 'vue-chartjs'

export default {
  extends: HorizontalBar,
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
  methods: {
    update (data) {
      this.selectedData = data
    }
  }
}
