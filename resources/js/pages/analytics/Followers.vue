<template>
  <div class="analytics-followers">
    <div class="followers-statistics">
      <div class="followers">
        <i class="fas fa-user"></i>
        <span class="number">{{Object.keys(followers).length}} </span>Followers
      </div>
      <div class="profile-views">
        <i class="fas fa-eye"></i>
        <span class="number">{{profileView}}</span>Profile Views
      </div>
    </div>
    <div class="followers-icons mt-3" v-if="followers.length > 0">
      <div class="follower" v-for="(value, key) in followers" v-if="key <= 5">
        <img  v-if="value.avatarUri" v-bind:class="{'margin-img': key != 0}"
             :src="'/storage/avatarImage/' + value.id + '/' + value.avatarUri"/>
        <img v-else v-bind:class="{'margin-img': key != 0}" src="/images/user8-128x128.png"/>
      </div>
      <button class="btn" @click="showModal = true">
        See all
        <i class="fas fa-arrow-right"></i>
      </button>
      <ModalMyFollowers :followers="followers"
        v-if="showModal"
        @close="showModal = false"
      ></ModalMyFollowers>
    </div>

    <!-- Select filters -->
    <div class="form-group mt-4 col-md-3">
      <v-select :options="filters"
                label="name"
                v-model="filter"
                @input ="change($event)"
                placeholder="MONTHS"
      >

      </v-select>
    </div>

    <bar-chart  :chart-data="data"  :height="100"  :options="optionsProject" ></bar-chart>
  <div>
    <div class="horizontal float-left">
      <h2 class="analytics">Social media</h2>
      <apexchart width="600" ref="social" type="bar" :options="chartOptions" :series="series"></apexchart>
    </div>

    <div class="mb-5 mt-5 map-div float-right">
      <GmapMap
        ref="maps"
        :center="{lat:-33.865143, lng:151.209900}"
        :zoom="7"
        map-type-id="terrain"
        style="width: 600px; height: 372px"
      >
        <GmapMarker
          :ref="`marker${index}`"
          :key="index"
          v-for="(m, index) in markers"
          :position="m.position"
          :clickable="true"
          :draggable="true"
          @click="center=m.position"
          @mouseover="openEventDetailModal(m)"

        />
      </GmapMap>
    </div>
  </div>

  <div>
    <div class="horizontal float-left">
      <h2 class="analytics age">Age</h2>
      <apexchart width="600" ref="age" type="bar" :options="ageChartOptions" :series="seriesAge"></apexchart>
    </div>


    <div class="float-right">
      <h2 class="analytics">Interest</h2>
      <apexchart width="600" ref="interest" type="bar" :options="interestChartOptions" :series="seriesInterest"></apexchart>
    </div>
  </div>

  </div>
</template>


<script>
  import BarChart from '../../mixins/barChart';
  import HorizontalBarChart from '../../mixins/horizontalBar';
  import ModalMyFollowers from "../../shared/modals/ModalMyFollowers.vue";
  import ApexCharts from 'apexcharts';
  import 'vue-select/dist/vue-select.css';
  export default {
    name: 'followers',
    middleware: 'auth',
    components: {
      BarChart,
      HorizontalBarChart,
      ModalMyFollowers,
      ApexCharts
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
        markers: [],
        showModal:false,
        data: [],
        followers: {},
        profileView: 0,
        projectId: '',
        ages: [],
        interests: [],
        writtenInterests: 0,
        audioInterests:   0,
        visualInterests:  0,
        eventsInterests:  0,

        chartOptions: {
          chart: {
            stacked: true,
          },
          plotOptions: {
            bar: {
              horizontal: true,
            },

          },
          stroke: {
            width: 1,
            colors: ['#fff']
          },

          xaxis: {
            categories: ['Tumblr', 'Twitter', 'Soundcloud', 'Spotify', 'Youtube', 'Vimeo', 'Behance', 'Linkedin', 'Etsy', 'Facebook', 'Instagram', 'Snapchat'],
          },

          tooltip: {
            y: {
              formatter: function (val) {
                return val + "K"
              }
            }
          },
          fill: {
            colors: ['#ff7271', '#ff7271', '#ff7271', 'ff7271', 'ff7271', 'ff7271', 'ff7271', 'ff7271', 'ff7271', 'ff7271', 'ff7271', 'ff7271'],
          },

          legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 40
          }
        },

        series: [{
          data: []
        }],


        ageChartOptions: {
          chart: {
            stacked: true,
          },
          plotOptions: {
            bar: {
              horizontal: true,
              columnHeight: '55%',
            },

          },
          stroke: {
            width: 1,
            colors: ['#fff']
          },

          xaxis: {
            categories: ['>14', '15-22', '23-30', '31-40', '41-50', '>51'],
          },

          tooltip: {
            y: {
              formatter: function (val) {
                return val + "K"
              }
            }
          },
          fill: {
            colors: ['#ff7271', '#ff7271', '#ff7271', 'ff7271', 'ff7271', 'ff7271'],
          },

          legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 40
          }
        },

        seriesAge: [{
          data: []
        }],


        interestChartOptions: {
          chart: {
            stacked: true,
          },
          plotOptions: {
            bar: {
              horizontal: true,
            },

          },
          stroke: {
            width: 1,
            colors: ['#fff']
          },

          xaxis: {
            categories: ['W', 'A', 'V', 'E'],
          },

          tooltip: {
            y: {
              formatter: function (val) {
                return val + "K"
              }
            }
          },
          fill: {
            colors: ['#ff7271', '#ff7271', '#ff7271', 'ff7271'],
          },

          legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 40
          }
        },


        seriesInterest: [{
          data: []
        }],

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

        dataCollectionFollowers: [],
        dataCollectionSoclia: [],

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
      getFollowers(filter) {
        let _this = this;
        axios.get(apiRoute + '/user/follows?filter='+filter, this.$store.getters['auth/token']).then(response => {
          _this.followers = response.data.followers;
          _this.profileView = response.data.profileView;
          _this.data = response.data.followersCount;
          _this.writtenInterests = response.data.writtenInterests;
          _this.audioInterests   = response.data.audioInterests;
          _this.visualInterests  = response.data.visualInterests;
          _this.eventsInterests  = response.data.eventsInterests;
          _this.seriesAge[0].data  = response.data.ages;

          response.data.markers.forEach((value, key) => {
            _this.markers.push({
              position: { lat: value.lat, lng: value.lng }
            })
          });

          _this.series[0].data  = response.data.resultDataCollectionSocial
          _this.seriesInterest[0].data = response.data.interests

          this.$refs.interest.updateOptions({ colors:  ['#ff7271', '#ff7271', '#ff7271', 'ff7271'] })
          this.$refs.age.updateOptions({ colors:  ['#ff7271', '#ff7271', '#ff7271', 'ff7271'] })
          this.$refs.social.updateOptions({ colors:  ['#ff7271', '#ff7271', '#ff7271', 'ff7271'] })
        }).catch(error => {

        })
      },

      onClick: function(evt, array) {
        if (array.length != 0) {
          var position = array[0]._index;
          var activeElement = this.tooltip._data.datasets[0].data[position]
        } else {

        }
      },

      change(){
        this.getFollowers(this.filter.id)
      }

    },

    created () {

    },

    mounted () {
      this.getFollowers()
    }
  }
</script>



<style>
  .apexcharts-menu-item{
    color:black !important;
  }
</style>


<style scoped lang="scss">
.followers-statistics {
  font-size: 1rem;
  text-align: center;
  color: #333;
  i {
    margin-right: 10px;
  }
  .number {
    font-family: EncodeSansSemiBold;
    font-size: 1.6rem;
    margin-right: 10px;
  }
}
.followers-icons {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-flow: row wrap;
  img {
    width: 40px;
    height: 40px;
    margin-right: 15px;
    border-radius: 50%;
  }
  .btn {
    color: #333;
    transition: 0.3s;
    &:hover {
      color: #9d72ff;
      transition: 0.3s;
    }
  }
}

  .analytics{
    color: #666666;
    font-size: 24px;
  }


  .horizontal{
    padding-left: 25px;
  }

  .age{
    margin-top: 35px;
  }


</style>




