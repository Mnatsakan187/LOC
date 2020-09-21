<template>
  <div class="container">
    <div class="buttons-block-map">
      <button @click="openFilter" class="btn btn-info">Filters</button>
      <button @click="openDateFilter" class="btn btn-info">Filters Date</button>
      <div class="view-switch  right justify-content-md-end justify-content-sm-start ">
        <a class="active">
          <i class="fas fa-map"></i>
        </a>
        <router-link   :to="{ name: 'events.list' }">
          <i class="fas fa-th-large"></i>
        </router-link>
        <router-link   :to="{ name: 'events.posters' }">
          <i class="far fa-file-image"></i>
        </router-link>
      </div>
    </div>
    <GmapMap
      ref="maps"
      :center="{lat:-33.865143, lng:151.209900}"
      :zoom="7"
      map-type-id="terrain"
      style="width: 1080px; height: 800px"
    >
      <GmapMarker
        :ref="`marker${index}`"
        :key="index"
        v-for="(m, index) in markers"
        :position="m.position"
        :clickable="true"
        :draggable="true"
        @drag="updateCoordinates"
        @click="center=m.position"
        @mouseover="openEventDetailModal(m)"

      />
    </GmapMap>
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
    <div class="modal bd-example-modal-sm" id="filterDate">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" @click="closeDate" data-dismiss="modal">&times;</button>
          </div>
          <!-- Modal body -->
          <div class="modal-body filter-date-body">
            <div>
              <p class="clear-all-filter"><span>CHOSE WHEN</span></p>
              <div>
                <div class="form-check">
                  <input type="checkbox" name="events"  @click="dateFilters(1)" id="all" class="form-check-input dateFiltersProp">
                  <label class="form-check-label" for="all" >All</label>
                </div>
              </div>
              <div>
                <div class="form-check">
                  <input type="checkbox" name="events"  id="today"  @click="dateFilters(2)"  class="form-check-input dateFiltersProp">
                  <label class="form-check-label" for="today">Today</label>
                </div>
              </div>
              <div>
                <div class="form-check">
                  <input type="checkbox" name="events"  @click="dateFilters(3)" id="tomorrow" class="form-check-input dateFiltersProp">
                  <label class="form-check-label" for="tomorrow">Tomorrow</label>
                </div>
              </div>
              <div>
                <div class="form-check">
                  <input type="checkbox" name="events"  id="this-week" @click="dateFilters(4)" class="form-check-input dateFiltersProp">
                  <label class="form-check-label" for="this-week">This week</label>
                </div>
              </div>
              <div style="max-width: 270px;; border-top: 1px solid black; margin-top: 10px; margin-bottom: 10px"></div>
              <div class="row" >
                <div class="form-group col-md-2">
                  <label >From</label>
                  <date-picker  v-model="fromDate" name="date start" :config="options"></date-picker>
                </div>

                <div class="form-group col-md-2">
                  <label >To</label>
                  <date-picker v-model="toDate" name="date end" :config="options"></date-picker>
                </div>
              </div>
            </div>
          </div>
          <!-- Modal footer -->
          <div class="modal-footer" >
            <button class="btn btn-primary button-long" href="#" @click="getEvents(2)" role="button">Apply</button>
            <button class="btn btn-link"  @click="closeDate" data-dismiss="modal">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
      <div class="dropdown-menu div-class event-info-detail">
        <div>
          <button type="button" class="close-popup" @click="closePopup" >&times;</button>
          <div class="event-info-poster d-flex justify-content-between">
            <div class="event-info">
              <h4>{{event.name}}</h4>
              <p>Created By - {{event.userFirstName}}   {{event.userLastName}}  </p>

              <p>{{event.category}} </p>
              <p>{{event.city}}</p>
              <p>{{event.startDate}}</p>
              <p>${{event.cost}}</p>
            </div>
            <div  class="event-poster">
              <img class="poster-image" v-if="event.posterUri" :src="'/storage/events/poster/'+event.id+'/'+event.posterUri"/>
              <img  v-else class="right" src="/images/event_poster.png"/>
            </div>
          </div>
          <div class="more-info">
            <span >More info</span>
            <i class="fas fa-plus-circle" @click="moreInfo"></i>
          </div>
          <div class="action-block clearfix">
            <div class="action-buttons float-right">
              <button :disabled="disabled" v-if="!event.likes" @click="like(event.id, false, 0, 'events')"><i  class="far fa-heart"></i></button>
              <button :disabled="disabled" v-else @click="like(event.id, true, event.likes.id, 'events')"><i  class="fas fa-heart isLike"></i></button>
            </div>
          </div>
        </div>
      </div>
    <!-- Event detail modal -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <div class="project-info">
              <h1>{{event.name}}</h1>
              <p>Created By - {{event.userFirstName}} {{event.userLastName}}</p>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body  project-detail" style="max-height: 964px">
            <div v-if="event.backgroundUri"  class="project-cover-holder project-cover-holder-modal" style="position: relative">
              <img :src="'/storage/events/background/' + event.id + '/' + event.backgroundUri"/>
            </div>
            <div v-else  class="project-cover-holder" style="position: relative">
              <img src="/images/event_cover.png"/>
              <p class="cover-image-p">Cover of the Event</p>
            </div>
            <div class="project-detail-text mt-3">
              <p>{{event.category}}</p>
              <p>Start Date - {{event.startDate}}</p>
              <p>End Date -{{event.endDate}}</p>
              <p>{{event.city}}</p>
              <p>Description of the event.</p>
              <p>{{event.description}}</p>
              <p>{{event.venue}}</p>
              <p>{{event.cost}}</p>
            </div>
            <div v-if="event.posterUri"  class="project-cover-holder project-cover-holder-modal" style="position: relative">
              <img :src="'/storage/events/poster/' + event.id + '/' + event.posterUri"/>
            </div>
            <div v-else  class="project-cover-holder project-cover-holder-modal" style="position: relative">
              <img src="/images/event_cover.png"/>
              <p class="cover-image-p" style="left: 46%;">Event Poster</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'events-map',
    middleware: 'auth',
    data: () => ({
      markers: [],
      events: {},
      selected: [],
      cost: '',
      userEvent: '',
      dateFilter: '',
      options: {
        format: 'YYYY-MM-DD',
      },
      fromDate: '',
      toDate: '',
      submitted: false,
      pageY: '',
      pageX: '',
      event: {},
      disabled: false,
      selectedData: [],
    }),

    methods: {
      validationAddGroup() {

      },

      getEvents(type) {
        $(".event-info-detail").hide()
        if(type != 1){
          this.markers = [];
        }

        let _this = this;
        axios.post(apiRoute + '/user/events/map', {
            categories: this.selected,
            cost:this.cost,
            userEvent:this.userEvent,
            dateFilter:this.selectedData,
            fromDate: this.fromDate,
            toDate: this.toDate,
        }, this.$store.getters['auth/token']).then(response => {

          _this.events = response.data.data;
          response.data.data.forEach((value, key) => {
            _this.markers.push({
              id: value.id,
              name: value.name,
              userFirstName:value.user.firstName,
              userLastName:value.user.lastName,
              category:value.category.name,
              city:value.city,
              description:value.description,
              startDate:value.startDate,
              endDate:value.endDate,
              venue:value.venue,
              cost:value.cost,
              posterUri:value.posterUri,
              backgroundUri:value.backgroundUri,
              likes:value.likes,
              position: { lat: value.latitude, lng: value.longitud }
            })
          });

        }).catch(error => {

        })
      },

      updateCoordinates(location){
        this.coordinates = {
          lat: location.latLng.lat(),
          lng: location.latLng.lng(),
        };
      },

      moreInfo(){
        $("#eventDetailModal").modal('show')
      },

      like(id, like, likeId, type) {
        this.disabled = true
        let _this = this;
        this.isActive = like;
        this.isActive = !this.isActive;
        if(this.isActive) {
          axios.post(apiRoute + '/user/'+type+'/'+id+'/likes', this.$store.getters['auth/token']).then(response => {
            _this.event.likes = response.data.data;
            _this.disabled = false
          }).catch(error => {

          })
        }else {
          axios.delete(apiRoute + '/user/'+type+'/'+id+'/likes/'+likeId, this.$store.getters['auth/token']).then(response => {
            _this.event.likes = '';
            _this.disabled = false
          }).catch(error => {

          })
        }
      },

      openEventDetailModal(event){
        this.event = event
        $('.event-info-detail').css('top', this.pageY);
        $('.event-info-detail').css('left', this.pageX);
        $(".event-info-detail").show()
      },

      costFilter(value){
        this.cost = value;
      },

      categoryFilter(el){
        if(this.selected.includes(el)){
          this.selected.splice(this.selected.indexOf(el), 1)
        }else {
          this.selected.push(el)
        }
      },

      openFilter() {
        $("#myModal").show()
        $("#filterDate").hide()
      },

      closePopup() {
        $(".event-info-detail").hide()
      },

      openDateFilter() {
        $("#filterDate").show()
        $("#myModal").hide()
      },

      close(){
        $("#myModal").hide()
      },

      closeDate(){
        $("#filterDate").hide()
      },


      dateFilters(value) {
        if(this.selectedData.includes(value)){
          this.selectedData.splice(this.selectedData.indexOf(value), 1)
        }else {
          this.selectedData.push(value)
        }
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

      allEvents(){
        this.cost = '';
        this.selected = [];
        this.userEvent = '';
        $(".events").prop('checked',false);
        $(".cost").prop('checked',false);
        $(".category").prop('checked',false);
      },


    },

    created() {
      this.groupId = this.$route.params.id
    },

    mounted() {
      let _this = this;
      this.getEvents(1);

      $(".events").change(function() {
        $(".events").prop('checked',false);
        $(this).prop('checked',true);
      });

      $(".cost").change(function() {
        $(".cost").prop('checked',false);
        $(this).prop('checked',true);
      });

      $(document).on('mousemove', function(e){
        _this.pageX = e.pageX - 194;
          _this.pageY = e.pageY- 258;
      });

    }
  }
</script>

<style scoped>
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

  .category {
    margin-top: 8px;
    margin-bottom: 5px;
    text-align: center;

  }

  .cost {
    margin-top: 8px;
    margin-bottom: 5px;
    text-align: center;
  }

  .modal-body {
    max-height: 800px;
  }

  .modal-footer{
    display: -webkit-inline-box;
  }

  .modal-header .close {
    font-size: 31px;
    color: #000000;
  }

  .modal-content{
    border-radius: 0.8rem;

  }
  .clear-all-filter{
    color: #0099CC;
    cursor: pointer;
  }

  .buttons-block-map{
    margin-bottom: 10px;
  }

  .filter-date-body{
    width: 800px;
  }

  #filterDate{
    margin-top: 140px;

  }

  .event-info-detail::after{
    display: inline-block;
    /* margin-left: -6.745em; */
    vertical-align: 0.255em;
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
    /* margin-top: 220px; */
    margin: auto;
    text-align: center;
    text-align: center;
    position: absolute;
    bottom: -18px;
    font-size: 55px;
    left: 170px;
    color: rgba(0, 0, 0, 0.349019607843137);

  }

  .event-info-detail{
    position: absolute;
    left: 0px;
    top: 0px;
    width: 400px;
    height: 229px;
    background: inherit;
    background-color: rgba(255, 255, 255, 1);
    box-sizing: border-box;
    border-width: 1px;
    border-style: solid;
    border-color: rgba(188, 188, 188, 1);
    border-radius: 0px;
    border-bottom-right-radius: 0px;
    border-bottom-left-radius: 0px;
    -moz-box-shadow: 0px -3px 5px rgba(0, 0, 0, 0.349019607843137);
    -webkit-box-shadow: 0px -3px 5px rgba(0, 0, 0, 0.349019607843137);
    box-shadow: 0px -3px 5px rgba(0, 0, 0, 0.349019607843137);

  }

  .event-info-poster .event-poster img {
    max-width: 174px;

  }

  .event-info-poster{
    padding: 14px;
  }

  .event-poster img{
    margin-top: 13px;
    height: 113px;
  }

  .close-popup{
    position: absolute;
    right: 12px;
    font-size: 26px;
    color: #0099cc;
    top: -2px;
  }

  .event-info-poster p {
    margin-bottom: 1px;
  }

  .more-info{
    margin-left: 16px;
  }

  .fa-plus-circle{
    color: #0099cc;
    font-size: 15px;
  }

  .action-buttons a {
    text-decoration: none;
    color: #0099CC;
    font-size: 1.1rem;
    margin-right: 5px;
    cursor: pointer;
  }

  .action-buttons a:hover {
    color: #5BB9E1;
  }


  .action-buttons button {
    text-decoration: none;
    color: #0099CC;
    font-size: 1.1rem;
    margin-right: 5px;
    cursor: pointer;
  }

  .action-buttons button:hover {
    color: #5BB9E1;
  }

  .isLike{
    color: red;
  }

  .fa-heart{
    font-size: 1.2rem;
  }

  .action-buttons{
    margin-top: -19px;
    margin-right: 14px
  }

  .project-cover-holder{
    margin: auto;
  }

  .project-detail{
    margin: auto;
  }


  .cover-image-p {
    position: absolute;
    top: 47%;
    left: 45%;
    font-size: 14px;
    color: #c3c4c3;
  }

  .project-cover-holder-modal img{
    width: 1000px;
    height: 321px;
  }

  .view-switch a {
    color: #999999;
    font-size: 1.5rem;
  }

  .view-switch a:nth-child(2) {
    margin-right: 10px;
    margin-left: 10px;
  }

  .view-switch a:hover {
    color: #7A7A7A;
  }

   .view-switch a.active {
    color: #309DD6;
    cursor: default;
  }

  button:focus {
    outline: none;
  }

</style>
