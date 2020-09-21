<template>
  <div class="analytics-followers">
    <!-- Select project -->
    <div class="form-group mt-4 col-md-6 select-project">
      <label class="sr-only">Select project</label>
      <v-select
        class="icon-select"
        v-model="projectId"
        name="project"
        :options="projects"
        label="name"
        placeholder="Select project"
        @input ="change(1)"
      >
        <template v-slot:selected-option="project">
          <div class="option-img-name">
            <img v-if="project.avatarUri" :src="'/storage/projects/projectAvatar/' + project.id + '/' + project.avatarUri"/>
            <img  v-else src="/images/user8-128x128.png"/>
            <span class="name">{{project.name}}</span>
          </div>
        </template>
        <template v-slot:option="project">
          <div class="option-img-name">
            <img v-if="project.avatarUri" :src="'/storage/projects/projectAvatar/' + project.id + '/' + project.avatarUri"/>
            <img  v-else src="/images/user8-128x128.png"/>
            <div class="name-title">
              <span class="name">{{project.name}}</span>
            </div>
          </div>
        </template>
      </v-select>
    </div>
    <!-- Select filters -->
    <div class="form-group mt-4 col-md-3" v-if="projectId">
      <v-select :options="filters"
                label="name"
                v-model="filter"
                @input ="changeFilter($event)"
                placeholder="MONTHS"
      >
      </v-select>
    </div>
    <div v-if="projectId">
      <bar-chart  :chart-data="data"  :height="100"  :options="optionsProject" ></bar-chart>
    </div>

    <div v-else>
      <div class="empty-content-discover">
        <div class="line bolder bolder-color">Please choose project</div>
      </div>
    </div>
  </div>
</template>

<script>
  import BarChart from '../../mixins/barChart';
  import HorizontalBarChart from '../../mixins/horizontalBar';
  import 'vue-select/dist/vue-select.css';
  export default {
    name: 'AnalyticsProjects',
    middleware: 'auth',
    components: {
      BarChart,
      HorizontalBarChart
    },
    data () {
      return {
        filters:[
          {
            "id": 1,
            "name": "MONTHS"
          },
          {
            "id": 2,
            "name": "WEEKS"
          },
          {
            "id": 3,
            "name": "DAYS"
          }
        ],
        filter:[],
        data: [],
        projects: [],
        projectId: '',
        optionsProject: {
          responsive: true,
          legend: {
            position: 'left',
          },

          title: {
            display: true,
            text: '',
            fontSize: 20
          },
          scales: {
            yAxes: [{ticks: {fontSize: 12, fontFamily: "'Roboto', sans-serif", fontColor: '#000', fontStyle: '500'}}],
            xAxes: [{ticks: {fontSize: 12, fontFamily: "'Roboto', sans-serif", fontColor: '#000', fontStyle: '500'}}]
          }
        },
        optionsProjectHorizontal: {

          responsive: true,
          legend: {
            display: false,
            position: 'left',
          },

          onClick: function(evt, array) {
            if (array.length != 0) {
              var position = array[0]._index;
              var activeElement = this.tooltip._data.datasets[0].data[position]
              $('#ht').modal('show')
              $('#modal-project-behaviours-' + this.tooltip._data.datasets[0].ids[position]).modal('show');
            } else {

            }
          },


          title: {
            display: false,
            text: '',
            fontSize: 20

          },
          scales: {
            xAxes: [ {gridLines: {display: false,  drawBorder: true,}} ],
            yAxes: [{gridLines: {display: false,  drawBorder: true}}, ]
          }
        },
        dataCollection: [],
        dataCollectionSoclia: {
          labels: ['Tumblr', 'Twitter', 'Soundcloud', 'Facebook'],
          datasets: [
            {
              backgroundColor: '#d7d7d7',
              pointBackgroundColor: 'white',
              borderWidth: 1,
              pointBorderColor: '#249EBF',
              data: [2.3, 3, 4, 6]
            },
          ]
        },

        options: {
          legend: false,
          tooltip: false,
          plugins: {
            datalabels: {
              align: function(context) {
                var index = context.dataIndex;
                var value = context.dataset.data[index];
                var invert = Math.abs(value) <= 1;
                return value < 1 ? 'end' : 'start'
              },
              anchor: 'end',
              backgroundColor: null,
              borderColor: null,
              borderRadius: 4,
              borderWidth: 1,
              color: '#223388',
              font: {
                size: 11,
                weight: 600
              },
              offset: 4,
              padding: 0,
              formatter: function(value) {
                return Math.round(value * 10) / 10
              }
            }
          }
        }

      }
    },

    methods: {
      getProjects() {
        let _this = this;
        axios.get(apiRoute + '/user/projects', this.$store.getters['auth/token']).then(response => {
          _this.projects = response.data.projects.data;
          _this.dataCollection = response.data.projectsHorizontalBar;
        }).catch(error => {

        })
      },

      change(filter) {
        let _this = this;
        axios.get(apiRoute + '/user/projects/'+this.projectId.id+'/'+filter, this.$store.getters['auth/token']).then(response => {
          this.data = response.data
        }).catch(error => {

        })
      },

      changeFilter(){
        this.change(this.filter.id)
      },

      onClick: function(evt, array) {
        if (array.length != 0) {
          var position = array[0]._index;
          var activeElement = this.tooltip._data.datasets[0].data[position]
        } else {

        }
      }

    },

    created () {

    },

    mounted () {
      this.getProjects()
    }
  }
</script>

<style lang="scss">
  .group-select {
    .vs__dropdown-toggle {
      font-size: 1.2rem;
      border: none !important;
      background-color: #e5e5e5;
      input {
        color: #323232;
      }
    }
    .vs__selected {
      font-size: 1.2rem;
      color: #323232 !important;
    }
    .vs__clear,
    .vs__open-indicator {
      fill: #323232 !important;
    }
    .vs__dropdown-menu {
      background-color: #e5e5e5 !important;
      .vs__dropdown-option {
        color: #323232;
        &:hover,
        &:focus,
        &.vs__dropdown-option--highlight {
          color: #fff;
          background-color: #323232 !important;
        }
      }
    }
  }
  .icon-select {
    .vs__dropdown-toggle {
      font-size: 1.2rem;
      border: none !important;
      background-color: #e5e5e5;
      input {
        color: #323232;
      }
    }
    .vs__selected {
      font-size: 1.2rem;
      color: #323232 !important;
    }
    .vs__clear,
    .vs__open-indicator {
      fill: #323232 !important;
    }
    .vs__dropdown-menu {
      background-color: #e5e5e5 !important;
      .vs__dropdown-option {
        color: #323232;
        &:hover,
        &:focus,
        &.vs__dropdown-option--highlight {
          color: #fff;
          background-color: #323232 !important;
        }
      }
    }
    .option-img-name {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 15px;
      }
      .name {
        display: block;
        font-size: 1.1rem;
        font-family: EncodeSansSemiBold;
      }
      .title {
        display: block;
        font-size: 1rem;
      }
    }
  }

  .select-project{
    margin: auto
  }

  .bolder-color{
    color: black !important;
  }
</style>

<style>
  .analytics-followers{
    height: calc(100vh - 60px);
  }

</style>


