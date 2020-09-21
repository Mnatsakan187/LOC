<template>
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
            <option value="1">Today</option>
            <option value="2">Tomorrow</option>
            <option value="3" selected>This week</option>
          </select>

          <!--Filter icon that triggers category filter -->
          <a href="#" @click="openModal" class="categories-filter"><i class="fas fa-filter"></i></a>
        </div>
      </div>
      <div class="col-6 order-first  order-md-last">
        <div class="view-switch right  justify-content-md-end justify-content-sm-start ">
          <router-link   :to="{ name: 'events.map' }">
            <i class="fas fa-map"></i>
          </router-link>
          <router-link   :to="{ name: 'events.list' }">
            <i class="fas fa-th-large"></i>
          </router-link>
          <a href="#" class="active"><i class="far fa-file-image"></i></a>
        </div>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col">
        <div class="posters-list">
          <div class="poster" v-for="event in events">
            <a href="#" data-toggle="modal" data-target="#modalPoster">
              <img v-if="!event.posterUri"  src="/images/poster1.png">
              <img v-else class="poster-image" :src="'/storage/events/poster/'+event.id+'/'+event.posterUri"/>
            </a>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal dialog: Poster-->
    <div class="modal fade" id="modalPoster" tabindex="-1" role="dialog" aria-labelledby="modalPosterLabel"
         aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
              <div class="carousel-inner">
                <template v-for="event, key in events">
                  <div class="carousel-item " v-bind:class="{active: key==0}">
                    <img v-if="!event.posterUri"  class="d-block w-100" src="/images/poster1.png" alt="First slide">
                    <img v-else class="d-block w-100" style="height: 372px" :src="'/storage/events/poster/'+event.id+'/'+event.posterUri"/>
                  </div>
                </template>
              </div>
              <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="center"  v-if="pagination.total > 0">
      <b-pagination
        v-model="pagination.current_page"
        :total-rows="pagination.total"
        :per-page="pagination.per_page"
        aria-controls="my-table"
        @change="getEvents"
      ></b-pagination>
    </div>
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
</template>
<script>
  export default {
    name: 'posters',
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
            userEvent:this.userEvent
          }, this.$store.getters['auth/token']).then(response => {
          _this.events = response.data.data;
          _this.pagination = response.data.meta;
        }).catch(error => {

        })
      },

      dateChange: function (date) {
        return moment(date).format('YYYY-MM-DD');
      },

      onChange(event) {
        this.day = event.target.value
        this.getEvents();
      },

      dateChangeThiweek: function (date) {
        return moment(date).format('dddd Do');
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
        let _this = this;
        this.isActive = like;
        this.isActive = !this.isActive;
        if(this.isActive) {
          axios.post(apiRoute + '/user/'+type+'/'+id+'/likes', this.$store.getters['auth/token']).then(response => {
            _this.getEvents();
          }).catch(error => {

          })
        }else {
          axios.delete(apiRoute + '/user/'+type+'/'+id+'/likes/'+likeId, this.$store.getters['auth/token']).then(response => {
            _this.getEvents();
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
  /* Event list styles START */
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


  .events-list .event {
    box-sizing: border-box;
    width: 350px;
    position: relative;
    display: inline-block;
    margin-right: 10px;
    margin-bottom: 10px;
    background-color: #F2F2F2;
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

  /* Event list styles END */

  /* Event poster styles START */
  .posters-list {
    box-sizing: border-box;
  }

  .poster {
    box-sizing: border-box;
    width: 350px;
    display: inline-block;
    margin-right: 7px;
    margin-bottom: 10px;
  }
  .poster img{
    width:100%;
    height: 350px;
  }

  .poster-image{
    height: 350px;
  }

  .center {
    margin: auto;
    width: 0%;
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

  .form-check-label {
    font-size: 12px;
  }

  /* Event poster styles END */
</style>
