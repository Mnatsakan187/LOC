<template>
  <!-- Main content-->
  <div class="container events">
    <div class="row">
      <div class="col-6">
        <div class="search-filters-area d-flex justify-content-sm-end justify-content-md-start align-items-center">
          <div class="form-group has-search d-none d-md-block">
            <span class="fa fa-search form-control-feedback"></span>
            <input type="text" v-model="search" @keyup="getEvents()" class="form-control" placeholder="Search">
          </div>

          <!--You need to integrate daterange picker here, that also supports shortcuts: today, tommorrow, this week, etc... -->
          <select class="form-control" @change="onChange($event)" id="selectDateRange">
            <option value="1">Today</option>
            <option value="2">Tomorrow</option>
            <option value="3" selected>This week</option>
          </select>

          <!--Filter icon that triggers category filter -->
          <a href="#" @click="openModal" class="categories-filter"><i class="fas fa-filter"></i></a>
        </div>
      </div>
      <div class="col-6 order-first order-md-last">
        <div class="view-switch  right justify-content-md-end justify-content-sm-start ">
          <router-link   :to="{ name: 'events.map' }">
            <i class="fas fa-map"></i>
          </router-link>
          <a href="#" class="active"><i class="fas fa-th-large"></i></a>
          <router-link   :to="{ name: 'events.posters' }">
            <i class="far fa-file-image"></i>
          </router-link>
        </div>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col">
        <h5 v-if="(day== 1 || day== 3) && today && todayLength">Today</h5>
        <div class="events-list">
            <div class="event" v-for="event, key in events" v-if="dateChange(event.startDate) == currectData()"  data-toggle="modal" :data-target="'#eventDetailModal-'+event.id" >
              <img v-if="!event.posterUri" src="/images/event-img.png">
              <img v-else class="poster-image" :src="'/storage/events/poster/'+event.id+'/'+event.posterUri"/>
              <div class="event-info">
                <div class="name-creator">
                  <h4>{{event.name}}</h4>
                  <div>Created by - {{event.user.firstName}} {{event.user.lastName}}</div>
                </div>
                <div class="time-location">
                  <div>Day and hour</div>
                  <div>{{event.streetAdress}}</div>
                  <div>{{event.city}}</div>
                </div>
                <button  :disabled="disabled"  class="event-like" v-if="!event.likes" @click="like(event.id, false, 0, 'events')"><i  class="far fa-heart"></i></button>
                <button :disabled="disabled"  class="event-like" v-else @click="like(event.id, true, event.likes.id, 'events')"><i  class="fas fa-heart isLike"></i></button>
              </div>

              <div class="modal fade" :id="'eventDetailModal-'+event.id" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
                   aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <div class="project-info">
                        <h1>{{event.name}}</h1>
                        <p>Created By - {{event.user.firstName}}</p>
                      </div>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body project-detail">
                      <div v-if="event.backgroundUri"  class="project-cover-holder" style="position: relative">
                        <img :src="'/storage/events/background/' + event.id + '/' + event.backgroundUri"/>
                      </div>
                      <div v-else  class="project-cover-holder" style="position: relative">
                        <img src="/images/event_cover.png"/>
                        <p class="cover-image-p">Cover of the Event</p>
                      </div>
                      <div class="project-detail-text mt-3">
                        <p>{{event.category.name}}</p>
                        <p>Start Date - {{event.startDate}}</p>
                        <p>End Date -{{event.endDate}}</p>
                        <p>{{event.city}}</p>
                        <p>Description of the event.</p>
                        <p>{{event.description}}</p>
                        <p>{{event.venue}}</p>
                        <p>{{event.cost}}</p>

                      </div>

                      <div v-if="event.posterUri"  class="project-cover-holder" style="position: relative">
                        <img :src="'/storage/events/poster/' + event.id + '/' + event.posterUri" style="height: 500px;"/>
                      </div>
                      <div v-else  class="project-cover-holder" style="position: relative">
                        <img src="/images/event_cover.png" style="height: 500px;"/>
                        <p class="cover-image-p" style="left: 46%;">Event Poster</p>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

        <h5  v-if="(day== 2 || day== 3) && tomorrow && tomorrowLength">Tomorrow</h5>
        <div  class="events-list" >
            <div class="event" v-for="event, key in events" v-if="dateChange(event.startDate) == tommorrowDay()"  data-toggle="modal" :data-target="'#eventDetailModal-'+event.id" >
              <img v-if="!event.posterUri" src="/images/event-img.png">
              <img v-else class="poster-image" :src="'/storage/events/poster/'+event.id+'/'+event.posterUri"/>
              <div class="event-info">
                <div class="name-creator">
                  <h4>{{event.name}}</h4>
                  <div>Created by - {{event.user.firstName}} {{event.user.lastName}}</div>
                </div>
                <div class="time-location">
                  <div>Day and hour</div>
                  <div>{{event.streetAdress}}</div>
                  <div>{{event.city}}</div>
                </div>
                <button :disabled="disabled"  class="event-like" v-if="!event.likes" @click="like(event.id, false, 0, 'events')"><i  class="far fa-heart"></i></button>
                <button :disabled="disabled" class="event-like" v-else @click="like(event.id, true, event.likes.id, 'events')"><i  class="fas fa-heart isLike"></i></button>
              </div>

              <div class="modal fade" :id="'eventDetailModal-'+event.id" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
                   aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <div class="project-info">
                        <h1>{{event.name}}</h1>
                        <p>Created By - {{event.user.firstName}}</p>
                      </div>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body project-detail">
                      <div v-if="event.backgroundUri"  class="project-cover-holder" style="position: relative">
                        <img :src="'/storage/events/background/' + event.id + '/' + event.backgroundUri"/>
                      </div>
                      <div v-else  class="project-cover-holder" style="position: relative">
                        <img src="/images/event_cover.png"/>
                        <p class="cover-image-p">Cover of the Event</p>
                      </div>
                      <div class="project-detail-text mt-3">
                        <p>{{event.category.name}}</p>
                        <p>Start Date - {{event.startDate}}</p>
                        <p>End Date -{{event.endDate}}</p>
                        <p>{{event.city}}</p>
                        <p>Description of the event.</p>
                        <p>{{event.description}}</p>
                        <p>{{event.venue}}</p>
                        <p>{{event.cost}}</p>

                      </div>

                      <div v-if="event.posterUri"  class="project-cover-holder" style="position: relative">
                        <img :src="'/storage/events/poster/' + event.id + '/' + event.posterUri" style="height: 500px;"/>
                      </div>
                      <div v-else  class="project-cover-holder" style="position: relative">
                        <img src="/images/event_cover.png" style="height: 500px;"/>
                        <p class="cover-image-p" style="left: 46%;">Event Poster</p>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

        <div    class="events-list">
            <div class="event"  v-for="event, key in events" v-if="dateChange(event.startDate) != tommorrowDay() && dateChange(event.startDate) != currectData()"    data-toggle="modal" :data-target="'#eventDetailModal-'+event.id">
            <h5  v-if="(day== 2 || day== 3)">{{dateChangeThiweek(event.startDate)}}</h5>
            <div class="event">
              <img v-if="!event.posterUri" src="/images/event-img.png">
              <img v-else class="poster-image" :src="'/storage/events/poster/'+event.id+'/'+event.posterUri"/>
              <div class="event-info">
                <div class="name-creator">
                  <h4>{{event.name}}</h4>
                  <div>Created by - {{event.name}}</div>
                </div>
                <div class="time-location">
                  <div>Day and hour</div>
                  <div>{{event.streetAdress}}</div>
                  <div>{{event.city}}</div>
                </div>
                <button :disabled="disabled"  class="event-like" v-if="!event.likes" @click="like(event.id, false, 0, 'events')"><i  class="far fa-heart"></i></button>
                <button :disabled="disabled"  class="event-like" v-else @click="like(event.id, true, event.likes.id, 'events')"><i  class="fas fa-heart isLike"></i></button>
              </div>
            </div>

            <div class="modal fade" :id="'eventDetailModal-'+event.id" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
                 aria-hidden="true">
              <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <div class="project-info">
                      <h1>{{event.name}}</h1>
                      <p>Created By - {{event.user.firstName}}</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body project-detail">
                    <div v-if="event.backgroundUri"  class="project-cover-holder" style="position: relative">
                      <img :src="'/storage/events/background/' + event.id + '/' + event.backgroundUri"/>
                    </div>
                    <div v-else  class="project-cover-holder" style="position: relative">
                      <img src="/images/event_cover.png"/>
                      <p class="cover-image-p">Cover of the Event</p>
                    </div>
                    <div class="project-detail-text mt-3">
                      <p>{{event.category.name}}</p>
                      <p>Start Date - {{event.startDate}}</p>
                      <p>End Date -{{event.endDate}}</p>
                      <p>{{event.city}}</p>
                      <p>Description of the event.</p>
                      <p>{{event.description}}</p>
                      <p>{{event.venue}}</p>
                      <p>{{event.cost}}</p>

                    </div>

                    <div v-if="event.posterUri"  class="project-cover-holder" style="position: relative">
                      <img :src="'/storage/events/poster/' + event.id + '/' + event.posterUri" style="height: 500px;"/>
                    </div>
                    <div v-else  class="project-cover-holder" style="position: relative">
                      <img src="/images/event_cover.png" style="height: 500px;"/>
                      <p class="cover-image-p" style="left: 46%;">Event Poster</p>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- The Modal -->
      <div class="modal bd-example-modal-sm" id="myModal">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" @click="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
              <div class="form-group has-search">
                <span class="fa fa-search form-control-feedback"></span>
                <input type="text" class="form-control" placeholder="Search">
              </div>
              <div>
                <p  @click="clearFilters()" class="clear-all-filter"><span>X Clear all filter</span></p>
                <div>
                  <div class="form-check">
                    <input type="checkbox" name="events" class="form-check-input events" id="allEvents">
                    <label class="form-check-label" @click="allEvents()" for="allEvents">All Events</label>
                  </div>
                </div>
                <div>
                  <div class="form-check">
                    <input type="checkbox" name="events" class="form-check-input events" id="myEvents">
                    <label class="form-check-label" @click="myEvents(1)" for="myEvents">My Events</label>
                  </div>
                </div>
                <div>
                  <div class="form-check">
                    <input type="checkbox" name="events" class="form-check-input events" id="myNetworkEvents">
                    <label class="form-check-label" for="myNetworkEvents">My Network Events</label>
                  </div>
                </div>
                <div>
                  <div class="form-check">
                    <input type="checkbox" name="events" class="form-check-input events" id="hosting">
                    <label class="form-check-label" for="hosting">Hosting</label>
                  </div>
                </div>
              </div>
              <div>
                <p class="category">CATEGORY</p>
                <div class="form-check">
                  <input type="checkbox" @click="categoryFilter(1)" class="form-check-input category" id="music">
                  <label class="form-check-label" for="music">Music</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" @click="categoryFilter(2)" class="form-check-input category" id="theatrePerformance">
                  <label class="form-check-label" for="theatrePerformance">Theatre & Performance
                  </label>
                </div>
                <div class="form-check">
                  <input type="checkbox" @click="categoryFilter(3)" class="form-check-input category" id="comedy">
                  <label class="form-check-label" for="comedy">Comedy</label>
                </div>
                <div>
                  <div class="form-check">
                    <input type="checkbox" @click="categoryFilter(4)"  class="form-check-input category" id="sporting">
                    <label class="form-check-label" for="sporting">Sporting</label>
                  </div>
                </div>
                <div class="form-check">
                  <input type="checkbox" @click="categoryFilter(5)"   class="form-check-input category" id="education">
                  <label class="form-check-label" for="education">Educational</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" @click="categoryFilter(6)"   class="form-check-input category" id="youth">
                  <label class="form-check-label" for="youth">Youth</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" @click="categoryFilter(7)"   class="form-check-input category" id="lecturesSeminars">
                  <label class="form-check-label" for="lecturesSeminars">
                    Lectures/Seminars
                  </label>
                </div>
                <div class="form-check">
                  <input type="checkbox"  @click="categoryFilter(8)"  class="form-check-input category" id="lifestyleExpo">
                  <label class="form-check-label" for="lifestyleExpo">
                    Lifestyle/Expo
                  </label>
                </div>
                <div class="form-check">
                  <input type="checkbox"  @click="categoryFilter(9)" class="form-check-input category" id="communityGovernment">
                  <label class="form-check-label" for="communityGovernment">
                    Community/Government
                  </label>
                </div>
              </div>
              <div>
                <p class="cost">COST</p>
                <div class="form-check">
                  <input type="checkbox" @click="costFilter(1)" class="form-check-input cost" id="free">
                  <label class="form-check-label"   for="free">Free</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" @click="costFilter(2)" class="form-check-input cost" id="ten">
                  <label class="form-check-label" for="ten">< $10
                  </label>
                </div>
                <div class="form-check">
                  <input type="checkbox" @click="costFilter(3)"  class="form-check-input cost" id="eleven">
                  <label class="form-check-label"  for="eleven">$11-$20</label>
                </div>
                <div>
                  <div class="form-check">
                    <input type="checkbox" @click="costFilter(4)" class="form-check-input cost" id="twennty">
                    <label class="form-check-label" for="twennty">$20+</label>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer" >
              <button class="btn btn-primary button-long" href="#" @click="getEvents(2)" role="button">Apply</button>
              <button class="btn btn-link" @click="close" data-dismiss="modal">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="center" v-if="pagination.total > 0">
      <b-pagination
        v-model="pagination.current_page"
        :total-rows="pagination.total"
        :per-page="pagination.per_page"
        aria-controls="my-table"
        @change="getEvents"
      ></b-pagination>
    </div>
  </div>
  <!-- Mobile footer ends-->
</template>

<script>
  export default {
    name: 'events',
    middleware: 'auth',
    data () {
      return {
        events: [],
        day: 3,
        search: '',
        pagination: {},
        cost: '',
        userEvent: '',
        selected: [],
        disabled: false,
        today: false,
        tomorrow: false,
        todayLength:0,
        tomorrowLength:0,
      }
    },

    methods: {
      getEvents () {
        let _this = this
        axios.post(apiRoute + '/user/events/list',
          {
            day:this.day,
            search:this.search,
            categories: this.selected,
            cost:this.cost,
            userEvent:this.userEvent,
          }, this.$store.getters['auth/token']).then(response => {
          _this.events = response.data.data;
          _this.pagination = response.data.meta;
          _this.todayLength = 0;
          _this.tomorrowLength = 0;
          _this.events.forEach((value, key) => {
              if(_this.dateChange(value.startDate) == _this.currectData()) {
                _this.today = true
                _this.todayLength ++
              }else if(_this.dateChange(value.startDate) == _this.tommorrowDay()){
                _this.tomorrow = true
                _this.tomorrowLength ++
              }

          })
        }).catch(error => {

        })
      },

      dateChange: function (date) {
        return moment(date).format('YYYY-MM-DD');
      },

      dateChangeThiweek: function (date) {
        return moment(date).format('dddd Do');
      },

      onChange(event) {
        this.day = event.target.value
        this.getEvents();
      },


      costFilter(value){
        this.cost = value;
      },

      currectData() {
        return moment().format('YYYY-MM-DD');
      },

      openModal(){
        $("#myModal").show()
      },

      close(){
        $("#myModal").hide()
      },

      tommorrowDay() {
        return moment().startOf('day').add(1, 'day').format('YYYY-MM-DD');
      },

      clearFilters() {
        this.cost = '';
        this.selected = [];
        this.userEvent = '';
        $(".events").prop('checked',false);
        $(".cost").prop('checked',false);
        $(".category").prop('checked',false);
        this.getEvents();
      },


      categoryFilter(el){
        if(this.selected.includes(el)){
          this.selected.splice(this.selected.indexOf(el), 1)
        }else {
          this.selected.push(el)
        }
      },

      allEvents(){
        this.cost = '';
        this.selected = [];
        this.userEvent = '';
        $(".events").prop('checked',false);
        $(".cost").prop('checked',false);
        $(".category").prop('checked',false);
      },

      myEvents(){
        this.userEvent = 1;
      },


      like(id, like, likeId, type) {
        this.disabled = true
        let _this = this;
        this.isActive = like;
        this.isActive = !this.isActive;
        if(this.isActive) {
          axios.post(apiRoute + '/user/'+type+'/'+id+'/likes', this.$store.getters['auth/token']).then(response => {
            _this.getEvents();
            this.disabled = false
          }).catch(error => {

          })
        }else {
          axios.delete(apiRoute + '/user/'+type+'/'+id+'/likes/'+likeId, this.$store.getters['auth/token']).then(response => {
            _this.getEvents();
            this.disabled = false
          }).catch(error => {

          })
        }

      },

    },

    created () {

    },

    mounted () {
      this.getEvents()
      $(".cost").change(function() {
        $(".cost").prop('checked',false);
        $(this).prop('checked',true);
      });

    }
  }
</script>

<style scoped>
  .events .has-search {
    max-width: 250px;
    margin-right: 15px;
    margin-bottom: 0px;
  }

  .events .has-search .form-control {
    padding-left: 2.375rem;
    border-radius: 25px;
  }

  .events .has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
  }

  .events select {
    width: 150px;
    margin-right: 15px;
  }

  .events .categories-filter {
    color: #999999;
    font-size: 1.5rem;
  }

  .events .view-switch a {
    color: #999999;
    font-size: 1.5rem;
  }

  .events .view-switch a:nth-child(2) {
    margin-right: 10px;
    margin-left: 10px;
  }

  .events .view-switch a:hover {
    color: #7A7A7A;
  }

  .events .view-switch a.active {
    color: #309DD6;
    cursor: default;
  }

  .events-list .event {
    width: 350px;
  }


  .events-list {
    box-sizing: border-box;
  }

  .events-list .event {
    box-sizing: border-box;
    width: 350px;
    position: relative;
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 10px;
    background-color: #F2F2F2;
  }

  .form-check-label {
    font-size: 12px;
  }

  .events-list .event img {
    width: 100%;
    padding-bottom: 10px;
  }

  .events-list .event .event-like {
    position: absolute;
    bottom: 10px;
    right: 10px;
    color: #80077F;
  }

  .events-list .event .name-creator {
    float: left;
    padding: 5px;
  }

  .events-list .event .time-location {
    float: left;
    margin-left: 10px;
    padding: 5px;
  }

  .center {
    margin: auto;
    width: 0%;
  }

  .poster-image{
    height: 106px !important;
    width: 350px !important;
  }


  .category {
    margin-top: 8px;
    margin-bottom: 5px;
    text-align: center;

  }

  .clear-all-filter{
    color: #0099CC;
    cursor: pointer;
    padding-top: 16px;

  }

  .has-search {
    max-width: 250px;
  }

  .has-search .form-control {
    padding-left: 2.375rem;
    border-radius: 25px;
  }

  .has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
  }

  button:focus {
    outline: none;
  }

  .cover-image-p {
    position: absolute;
    top: 47%;
    left: 45%;
    font-size: 14px;
    color: #c3c4c3;
  }

  .event:hover{
    cursor: pointer;
  }

</style>
