<template>
  <div class="container">
    <div>
      <FullCalendar
        class="demo-app-calendar"
        ref="fullCalendar"
        defaultView="dayGridMonth"
        :header="header"
        :plugins="calendarPlugins"
        :weekends="calendarWeekends"
        :events="events"
        :button-text="buttonText"
        :custom-buttons="customButtons"
        @dateClick="handleDateClick"
      />
      <!-- The Modal -->
      <div class="modal bd-example-modal-sm right" id="myModal">
        <div class="modal-dialog modal-sm right">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                    <label class="form-check-label" for="sporting">Hosting</label>
                  </div>
                </div>
                <div class="form-check">
                  <input type="checkbox" @click="categoryFilter(5)"   class="form-check-input category" id="education">
                  <label class="form-check-label" for="education">Hosting</label>
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
              <button class="btn btn-primary button-long" href="#" @click="getEvents" role="button">Apply</button>
              <button class="btn btn-link" data-dismiss="modal">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>

</template>

<script>
  import FullCalendar from '@fullcalendar/vue'
  import dayGridPlugin from '@fullcalendar/daygrid'
  import timeGridPlugin from '@fullcalendar/timegrid'
  import interactionPlugin from '@fullcalendar/interaction'

  export default {
    components: {
      FullCalendar
    },
    middleware: 'auth',
    data () {
      return {
        selected: [],
        cost: '',
        userEvent: '',
        calendarPlugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin
        ],
        header: {
          left: 'prev,next today myCustomButton',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay, agendaDay, add_event'
        },
        calendarWeekends: true,
        events: [],
        buttonText: {
          today: 'Today',
          month: 'Month',
          week: 'Week',
          day: 'Day',
        },

        customButtons: {
          add_event: {
            text: 'Filters',
            click: function() {
              $('#myModal').modal('show')
            }
          },
        },
      }
    },

    methods: {
      getEvents () {
        let _this = this
        axios.get(apiRoute + '/user/events/calendar?categories='+this.selected+ '&cost='+this.cost + '&userEvent='+this.userEvent, this.$store.getters['auth/token']).then(response => {
          _this.events = response.data
        }).catch(error => {

        })
      },

      handleDateClick () {

      },

      categoryFilter(el){
        if(this.selected.includes(el)){
          this.selected.splice(this.selected.indexOf(el), 1)
        }else {
          this.selected.push(el)
        }
      },

      myEvents(){
        this.userEvent = 1;
      },

      costFilter(el) {
        this.cost = el;
      },

      allEvents(){
        this.cost = '';
        this.selected = [];
        this.userEvent = '';
        $(".events").prop('checked',false);
        $(".cost").prop('checked',false);
        $(".category").prop('checked',false);
      },

      clearFilters() {
        this.cost = '';
        this.selected = [];
        this.userEvent = '';
        $(".events").prop('checked',false);
        $(".cost").prop('checked',false);
        $(".category").prop('checked',false);
        this.getEvents();
      }
    },

    created () {

    },

    mounted () {
      this.getEvents()

      $(".events").change(function() {
        $(".events").prop('checked',false);
        $(this).prop('checked',true);
      });

      $(".cost").change(function() {
        $(".cost").prop('checked',false);
        $(this).prop('checked',true);
      });

    }
  }

</script>

<style lang='scss'>
  @import '~@fullcalendar/core/main.css';
  @import '~@fullcalendar/daygrid/main.css';
  @import '~@fullcalendar/timegrid/main.css';

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


  .modal-backdrop{
    height: 0px;
  }
</style>
