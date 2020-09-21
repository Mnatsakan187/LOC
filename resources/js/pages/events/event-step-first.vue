<template>
    <!-- Main content-->
    <div class="container">
      <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-5 offset-lg-3">
          <div class="d-none d-md-block">
            <h1>New Event</h1>

            <div class="start-text">
              <p>Lorem ipsum dolor sit amet.</p>
            </div>
          </div>
          <div class="create-post-area" onsubmit="return false">
            <form>
              <div class="form-group">
                <label for="name">Event name</label>
                <input class="form-control"  v-validate="'required'"  v-model="event.name" id="name" name="name" rows="7">
                <span class="error-message" v-if="submitted && errors.has('name')">{{ errors.first('name') }}</span>
                <span class="error-message" v-text="error.get('name')"></span>
              </div>

              <div class="form-group mt-4">
                <label for="category">Category</label>
                <select v-validate="'required'" v-model="event.categoryId" class="form-control col-md-8" name="category" id="category">
                  <option value="">Select a category</option>
                  <option v-for="category in categories" :value="category.id">{{category.name}}</option>
                </select>
                <span class="error-message" v-if="submitted && errors.has('category')">{{ errors.first('category') }}</span>
              </div>
              <div class="row">
                <div class="form-group col-xs-6 ml-3">
                  <label for="name">Date Start</label>
                  <date-picker v-validate="'required'" v-model="event.startDate" name="date start" :config="options"></date-picker>
                  <span class="error-message" v-if="submitted && errors.has('date start')">{{ errors.first('date start') }}</span>
                </div>

                <div class="form-group col-xs-6  ml-4">
                  <label for="name">Date End</label>
                  <date-picker v-validate="'required'" v-model="event.endDate" name="date end" :config="options"></date-picker>
                  <span class="error-message" v-if="submitted && errors.has('date end')">{{ errors.first('date end') }}</span>
                </div>
              </div>
              <div class="form-group">
                <label>Address</label>
                <vue-google-autocomplete
                  ref="address"
                  id="map"
                  v-model="event.venue"
                  classname="form-control"
                  placeholder="Please type your address"
                  v-on:placechanged="getAddressData"
                >
                </vue-google-autocomplete>
                <input type="hidden"  class="form-control" v-validate="'required'"  v-model="event.venue" name="where"/>
                <span class="error-message" v-if="submitted && errors.has('where')">{{ errors.first('where') }}</span>
              </div>

              <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control"  v-validate="'required|max:1000'"  id="description"
                          v-model="event.description"  name="description" rows="7"></textarea>
                <span class="error-message" v-if="submitted && errors.has('description')">{{ errors.first('description') }}</span>
              </div>

              <div class="form-group">
                <label>Price:</label>
                <input class="form-control col-md-4"  v-validate="'required|decimal'"  v-model="event.cost" id="price" name="price">
                <span class="error-message" v-if="submitted && errors.has('price')">{{ errors.first('price') }}</span>
              </div>


              <div class="form-group mt-4">
                <label for="profile">Profiles</label>
                <select v-validate="'required'" v-model="event.profileId" class="form-control" name="profile" id="profile">
                  <option value="">Select a profile</option>
                  <option v-for="profile in profiles" :value="profile.id">{{profile.creativeTitle}}</option>
                </select>
                <span class="error-message" v-if="submitted && errors.has('profile')">{{ errors.first('profile') }}</span>
              </div>

              <!-- Submit Buttons -->
              <div class="mt-5 d-none d-md-block">
                <a class="btn btn-primary" @click="validationEventFirstStep" role="button">Continue</a>
                <a   class="btn btn-link"  data-toggle="modal" data-target="#cancelEventModal" role="button">Cancel</a>
              </div>
              <div class="create-footer-nav fixed-bottom d-block d-md-none pt-3 pb-3">
                <a class="btn btn-link"  role="button" data-toggle="modal" data-target="#cancelEventModal">Cancel</a>
                <a class="btn btn-primary  button-long" @click="validationEventFirstStep" role="button">Continue</a>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="cancelEventModal" tabindex="-1" role="dialog" aria-labelledby="cancelEventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="cancelEventModalLabel">Modal title</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Are you sure you want to cancel the window? Your data will be deleted
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
              <button type="button" @click="cancelWindow" class="btn btn-primary">YES</button>
            </div>
          </div>
        </div>
      </div>

    </div>
</template>

<script>
  class error {
    constructor() {
      this.error = {};
    }

    get(field) {
      if (this.error[field]) {
        return this.error[field][0];
      }
    }

    record(error) {
      this.error = error;
    }
  }
  import VueGoogleAutocomplete from 'vue-google-autocomplete'
  import 'vue-select/dist/vue-select.css';
  export default {
    name: 'event-step-first',
    props: ['prevRoute', 'eventData'],
    middleware: 'auth',
    components: { VueGoogleAutocomplete },
    data: () => ({
      users: [],
      submitted:false,
      error: new error(),
      profiles: {},
      next: 2,
      categories: {},
      cities: [],
      event: {
        name: '',
        categoryId: '',
        venue: '',
        streetAdress: '',
        city: '',
        description: '',
        cost: '',
        profileId: '',
        startDate: '',
        endDate: '',
        latitude: '',
        longitude: '',
        address: '',
      },
      options: {
        format: 'YYYY-MM-DD HH:mm:ss',
      },


    }),

    methods: {
      validationEventFirstStep() {
        this.submitted = true;
        let _this = this;
        this.$validator.validate().then(valid => {


          if (valid) {
            this.$emit('event', this.event);
            this.$emit('validationEventStep', {step:this.next,   eventData: {
                event:this.event,
                imgSrc: this.eventData.imgSrc,
                cropImg:this.eventData.cropImg,
                backgroundUri: this.eventData.backgroundUri,
                posterUri: this.eventData.posterUri,
                posterImgSrc: this.eventData.posterImgSrc,
                posterCropImg: this.eventData.posterCropImg
              }});
          }
        });
      },

      getProfiles() {
        let _this = this;
        axios.get(apiRoute + '/user/profiles', this.$store.getters['auth/token']).then(response => {
          _this.profiles = response.data.data;
        }).catch(error => {

        })
      },

      getCategories() {
        let _this = this;
        axios.get(apiRoute + '/categories', this.$store.getters['auth/token']).then(response => {
          _this.categories = response.data.data;
        }).catch(error => {

        })
      },

      cancelWindow() {
        $("#cancelEventModal").modal('hide');
        this.$router.push({ path: this.prevRoute})
      },

      getAddressData: function (addressData, placeResultData, id) {
        this.event.venue = addressData.route +','+addressData.locality +','+addressData.country;
        this.event.latitude = addressData.latitude
        this.event.longitude = addressData.longitude
        this.address =  addressData.route +','+addressData.locality +','+addressData.country
      }
    },

    created() {

    },

    mounted() {
      this.cities = cities;
      this.getProfiles();
      this.getCategories();
      if(this.eventData != ''){
        this.event = this.eventData.event
      }

      this.$refs.address.focus();

    }
  }

</script>

<style scoped>
  .create-post-area .add-option i{
    font-size: 2rem;
    margin-right: 10px;
  }

  .post-cover-holder img{
    width: 100%;
  }

  .error-message {
    color: red;
  }

</style>
