<template>
  <div class="analytics-polls">
    <!-- Select poll -->
    <div class="form-group mt-4 col-md-6 select-poll mb-5">
      <label class="sr-only">Select poll</label>
      <v-select
        class="icon-select"
        v-model="pollId"
        name="project"
        :options="polls"
        label="name"
        placeholder="Select poll"
        @input ="change($event)"
      >
        <template v-slot:selected-option="poll">
          <div class="option-img-name">
            <span class="name">{{poll.name}}</span>
          </div>
        </template>
        <template v-slot:option="poll">
          <div class="option-img-name">
            <div class="name-title">
              <span class="name">{{poll.name}}</span>
            </div>
          </div>
        </template>
      </v-select>
    </div>
    <h2 class="mt-5 mb-5 question-color">{{poll.question}}</h2>

    <div v-if="pollId">
      <div v-if="poll.answer_type == 2" >
        <PieChart :chartData="chartData" :width="80" :height="20" :options="chartOptions"></PieChart>
      </div>

      <div  v-if="poll.answer_type == 1">
        <div>
          <div class="members">
            <template v-for="answer in chartData">
              <p>{{answer.user.first_name}} {{answer.user.last_name}}</p>
              <img  v-if="answer.user.avatarUri" :src="'/storage/avatarImage/' + answer.user.id + '/' + answer.user.avatarUri"/>
              <img v-else  src="/images/user8-128x128.png"/>

              <p><b>Answer: {{answer.open_answer}}</b></p>

            </template>
          </div>
        </div>

      </div>
    </div>

    <div v-else>
      <div class="empty-content-discover">
        <div class="line bolder bolder-color">Please choose poll</div>
      </div>
    </div>

  </div>
</template>

<script>
  import PieChart from '../../mixins/pieChart';
  import 'vue-select/dist/vue-select.css';
  export default {
    name: "App",
    middleware: 'auth',
    components: {
      PieChart
    },
    data() {
      return {
        chartOptions: {
          hoverBorderWidth: 20
        },
       chartData: [],
        polls: [],
        pollId: '',
        poll: ''
      };
    },

    methods:{
      getPolls() {
        let _this = this;
        axios.get(apiRoute + '/user/polls', this.$store.getters['auth/token']).then(response => {
          _this.polls = response.data.data;
        }).catch(error => {

        })
      },

      change(){
        axios.get(apiRoute + '/user/polls/diagram/'+this.pollId.id, this.$store.getters['auth/token']).then(response => {
          this.chartData = response.data.pollPieChart
          this.poll = response.data.poll
        }).catch(error => {

        })
      }
    },

    mounted(){
      this.getPolls();
    }
  };
</script>

<style scoped lang="scss">
  .members {
    justify-content: flex-start;
    text-align: center;
    img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
    }
    .more-members {
      color: #fff;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #333;
      display: flex;
      justify-content: center;
      align-content: center;
      flex-direction: column;
      text-align: center;
    }
  }

  .select-poll{
    margin: auto
  }

  .bolder-color{
    color: black !important;
  }

  .members p {
    color:  #2c3e50;
  }

  .analytics-polls{
    height: calc(100vh - 60px) !important;
  }

  .question-color{
    color: black;
    text-align: center;
  }
</style>


