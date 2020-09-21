<template>
  <!-- Main content-->
  <div class="container pt-1">
    <div class="row pt-5">
      <div class="col-md-8 offset-md-2 col-lg-5 offset-lg-3">
        <div class="d-none d-md-block">
          <h1>Edit event</h1>

          <div class="start-text">
            <p>Lorem ipsum dolor sit amet.</p>
          </div>
        </div>
        <div class="create-post-area" onsubmit="return false">
          <form>
            <div class="event-desktop-header d-md-block mb-4">
              <div  class="buttons-block mb-3">
              </div>
              <div class="cover-image-block text-center">
                <img v-if="!cropImg" src="/images/cover.png" class="cover-image">
                <img v-else :src="cropImg" class="cover-image">
                <p v-if="!cropImg" class="cover-image-p">Cover of the Event</p>
                <div class="event-content">
                  <h2>{{event.name}}</h2>
                  <p>{{event.category.name}}</p>
                  <p>{{event.city}}</p>
                </div>
              </div>

              <div data-toggle="modal" data-target="#eventCovarModal" class="add-cover-image">
                <i class="fas fa-plus-circle"></i>
                <span v-if="!cropImg">Add Cover Image</span>
                <span v-else>Edit Cover Image</span>
              </div>
            </div>

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

            <div>
              <div data-toggle="modal" data-target="#eventPosterModal" class="add-poster-image mb-1">
                <i class="fas fa-plus-circle"></i>
                <span v-if="!posterCropImg">Add Event Poster</span>
                <span v-else>Edit Event Poster</span>
              </div>
              <img v-if="posterCropImg" :src="posterCropImg" class="cover-image">
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
              <a class="btn btn-primary" @click="edit" role="button">Finish</a>
              <router-link   class="btn btn-link" :to="{ path: prevRoute}"  >Cancel</router-link>
            </div>
            <div class="create-footer-nav fixed-bottom d-block d-md-none pt-3 pb-3">
              <router-link class="btn btn-link" :to="{ path: prevRoute}" >Cancel</router-link>
              <a class="btn btn-primary  button-long" @click="edit" role="button">Finish</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="eventCovarModal" tabindex="-1" role="dialog" aria-labelledby="coverModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="coverModalLabel">Upload Image</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <hr />
            <input
              type="file"
              name="image"
              accept="image/*"
              style="font-size: 1.2em; padding: 10px 0;"
              @change="setImage"
            />
            <br />
            <div v-if="imgSrc"
                 style="width: 400px; height:300px; border: 1px solid gray; display: inline-block;"
            >
              <vue-cropper
                ref="cropper"
                :guides="false"
                :view-mode="3"
                drag-mode="crop"
                :auto-crop-area="0.5"
                :min-container-width="400"
                :min-container-height="300"
                :background="true"
                :aspectRatio="3"
                :src="imgSrc"
                alt="Source Image"
                :img-style="{ width: '400px', height: '300px' }"
              >
              </vue-cropper>
            </div>
            <br />
            <br />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" @click="cropImage" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="eventPosterModal" tabindex="-1" role="dialog" aria-labelledby="posterModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="posterModalLabel">Upload Image</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <hr />
            <input
              type="file"
              name="images"
              accept="image/*"
              style="font-size: 1.2em; padding: 10px 0;"
              @change="setPosterImage"
            />
            <br />
            <div v-if="posterImgSrc"
                 style="width: 400px; height:300px; border: 1px solid gray; display: inline-block;"
            >
              <vue-cropper
                ref="cropperPoster"
                :guides="false"
                :view-mode="3"
                drag-mode="crop"
                :auto-crop-area="0.5"
                :min-container-width="400"
                :min-container-height="300"
                :background="true"
                :aspectRatio="3"
                :src="posterImgSrc"
                alt="Source Image"
                :img-style="{ width: '400px', height: '300px' }"
              >
              </vue-cropper>
            </div>
            <br />
            <br />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" @click="cropPosterImage" class="btn btn-primary">Save changes</button>
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
  import VueCropper from 'vue-cropperjs';
  import {helpers} from '../../mixins/helpers';
  import VueGoogleAutocomplete from 'vue-google-autocomplete'
  import 'vue-select/dist/vue-select.css';
  export default {
    name: 'edit',
    components: {VueCropper, VueGoogleAutocomplete},
    middleware: 'auth',
    mixins: [helpers],
    data: () => ({
      users: [],
      submitted:false,
      error: new error(),
      profiles: {},
      next: 2,
      categories: {},
      eventId: '',
      profileId: '',
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
        category: {},
        latitude: '',
        longitude: ''
      },
      prevRoute: '',
      options: {
        format: 'YYYY-MM-DD HH:mm:ss',
      },
      imgSrc: '',
      cropImg: '',
      backgroundUri: '',
      posterUri: '',
      posterImgSrc:'',
      posterCropImg: '',
      cities: [],

    }),

    beforeRouteEnter(to, from, next) {
      next(vm => {
        if(from.path != '/') {
          vm.prevRoute = from.path;
        }else{
          vm.prevRoute = '/home';
        }
      })
    },

    methods: {
      edit() {
        this.submitted = true;
        let _this = this;
        this.$validator.validate().then(valid => {
          if (valid) {
            let formData = new FormData();
            formData.append('name', this.event.name);
            formData.append('categoryId',this.event.categoryId);
            formData.append('venue', this.event.venue);
            formData.append('streetAdress',this.event.streetAdress);
            formData.append('city', this.event.city);
            formData.append('description', this.event.description);
            formData.append('cost', this.event.cost);
            formData.append('startDate', this.event.startDate);
            formData.append('endDate', this.event.endDate);
            formData.append('backgroundUri', this.backgroundUri);
            formData.append('posterUri', this.posterUri);
            formData.append('latitude', this.event.latitude);
            formData.append('longitude', this.event.longitude);
            axios.post(apiRoute + '/user/profiles/'+this.event.profileId+'/events/'+this.eventId+'/update', formData, this.$store.getters['auth/token']).then(response => {
              this.$router.push({ path: this.prevRoute})
            }).catch(error => {

            })
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

      getEvent() {
        let _this = this;
        axios.get(apiRoute + '/user/profiles/'+this.profileId+'/events/'+this.eventId, this.$store.getters['auth/token']).then(response => {
          _this.event = response.data.data;
          if(response.data.data.posterUri){
            _this.posterCropImg = '/storage/events/poster/' + response.data.data.id + '/' + response.data.data.posterUri;
            _this.posterImgSrc = '/storage/events/poster/' + response.data.data.id + '/' + response.data.data.posterUri;
          }
          if(response.data.data.backgroundUri) {
            _this.cropImg = '/storage/events/background/' + response.data.data.id + '/' + response.data.data.backgroundUri;
            _this.imgSrc = '/storage/events/background/' + response.data.data.id + '/' + response.data.data.backgroundUri;
          }
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

      setImage(e) {
        let _this = this;
        const file = e.target.files[0];
        this.imgSrc = '';
        if (!file.type.includes("image/")) {
          alert("Please select an image file");
          return;
        }

        if (typeof FileReader === "function") {
          const reader = new FileReader();


          reader.onload = event => {
            this.imgSrc = event.target.result;

            if(this.$refs.cropper != undefined) {
              this.$refs.cropper.replace(event.target.result);
            }
          };
          reader.readAsDataURL(file);
        } else {
          alert("Sorry, FileReader API not supported");
        }
      },


      cropImage() {
        this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
        $("#eventCovarModal").modal('hide');
        this.backgroundUri = this.dataURLtoFile(this.cropImg , 'file');
      },


      setPosterImage(e) {
        const file = e.target.files[0];
        if (!file.type.includes("image/")) {
          alert("Please select an image file");
          return;
        }

        if (typeof FileReader === "function") {
          const reader = new FileReader();

          reader.onload = event => {
            this.posterImgSrc = event.target.result;
            if(this.$refs.cropperPoster != undefined) {
              this.$refs.cropperPoster.replace(event.target.result);
            }
          };
          reader.readAsDataURL(file);
        } else {
          alert("Sorry, FileReader API not supported");
        }
      },


      cropPosterImage() {
        this.posterCropImg = this.$refs.cropperPoster.getCroppedCanvas().toDataURL();
        $("#eventPosterModal").modal('hide');
        this.posterUri = this.dataURLtoFile(this.posterCropImg, 'file');
      },

      getAddressData: function (addressData, placeResultData, id) {
        this.event.venue = addressData.route +','+addressData.locality +','+addressData.country;
        this.event.latitude = addressData.latitude
        this.event.longitude = addressData.longitude
        this.address = addressData
      }

    },

    created() {
      this.eventId = this.$route.params.eventId
      this.profileId = this.$route.params.profileId
    },

    mounted() {
      this.cities = cities;
      this.getProfiles();
      this.getCategories();
      this.getEvent();

      this.$refs.address.focus();

    }
  }
</script>

<style scoped>
  /* MOBILE Profile Detail Header Styles START */
  .cover-image-block {
    position: relative;
  }

  .event-desktop-header{
    position: relative;
  }

  .cover-image-block .profile-image-name {
    position: absolute;
    top: 10px;
    left: 10px;
    color: #fff;
  }

  .cover-image-block .edit-my-profile {
    position: absolute;
    bottom: 10px;
    left: 10px;
  }

  /* MOBILE Profile Detail Header Styles START */
  .cover-image-block {
    position: relative;
  }

  .cover-image-block .profile-image-name {
    position: absolute;
    top: 10px;
    left: 10px;
    color: #fff;
  }

  .cover-image-block .edit-my-profile {
    position: absolute;
    bottom: 10px;
    left: 10px;
  }


  .cover-image {
    width: 100%;
    height: 260px;
  }

  .cover-image-p {
    position: absolute;
    top: 46%;
    left: 40%;
    font-size: 14px;
    color: #c3c4c3;
  }

  .event-content{
    position: absolute;
    top: 10%;
    left: 5%;
    text-align: left;
  }

  .event-content p {
    font-size: 12px;
    margin-bottom: 0px;

  }


  .add-cover-image {
    position: absolute;
    top: 75%;
    left: 36%;
    margin-right: auto;
    margin-left: auto;
    color: #159BD5;
    cursor: pointer;
    display: flex;
    align-items: center;
  }

  .add-cover-image i {
    font-size: 2rem;
    margin-right: 10px;
  }

  .add-poster-image{
    position: relative;
    color: #159BD5;
    cursor: pointer;
    align-items: center;
    display: block;
    text-align: center;
    margin-right: 108px;
  }

  .add-poster-image i {
    font-size: 2rem;
    margin-right: 10px;
  }

  .add-poster-image span {
    position: absolute;
    top: 10%;
  }

  .modal-body{
    margin: auto;
  }
</style>
