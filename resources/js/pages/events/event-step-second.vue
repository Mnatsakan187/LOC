<template>
  <!-- Main content-->
  <div class="container pt-1">
    <div class="row pt-5">
      <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
        <div class="d-none d-md-block">
          <h1>New Event</h1>
          <div class="start-text">
            <p>Lorem ipsum dolor sit amet.</p>
          </div>
        </div>
        <div class="create-post-area" onsubmit="return false">
          <form>
            <div class="event-desktop-header d-md-block">
              <div  class="buttons-block mb-3">
              </div>
              <div class="cover-image-block text-center">
                <img v-if="!cropImg" src="/images/cover.png" class="cover-image">
                <img v-else :src="cropImg" class="cover-image">
                <p v-if="!cropImg" class="cover-image-p">Cover of the Event</p>
                <div class="event-content">
                  <h2>{{eventData.event.name}}</h2>
                  <span class="error-message" v-text="error.get('name')"></span>
                  <p>{{eventData.event.category}}</p>
                  <p>{{eventData.event.city}}</p>
                </div>
              </div>

              <div data-toggle="modal" data-target="#eventCovarModal" class="add-cover-image">
                <i class="fas fa-plus-circle"></i>
                <span v-if="!cropImg">Add Cover Image</span>
                <span v-else>Edit Cover Image</span>
              </div>
            </div>

            <div class="mt-3">
              <p>Description of the event.</p>
              <p>
                {{eventData.event.description}}
              </p>
            </div>

            <div class="mt-3">
              <p>{{eventData.event.venue}}</p>
              <p>{{eventData.event.cost}}</p>
            </div>

            <div>
              <div data-toggle="modal" data-target="#eventPosterModal" class="add-poster-image mb-1">
                <i class="fas fa-plus-circle"></i>
                <span v-if="!posterCropImg">Add Event Poster</span>
                <span v-else>Edit Event Poster</span>
              </div>
              <img v-if="posterCropImg" :src="posterCropImg" class="cover-image">
            </div>

            <!-- Submit Buttons -->
            <div class="mt-5 d-none d-md-block">
              <a class="btn btn-primary" @click="validationEventSecondStep" role="button">Finish</a>
              <a   class="btn btn-link"  @click="back" role="button">Back</a>
            </div>
            <div class="create-footer-nav d-block d-md-none pt-3 pb-3">
              <a class="btn btn-link"  role="button" @click="back">Back</a>
              <a class="btn btn-primary  button-long" @click="validationEventSecondStep" role="button">Finish</a>
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
                ref="posterCropperImage"
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
  export default {
    name: 'event-step-second',
    props: ['prevRoute', 'eventData'],
    components: {VueCropper},
    middleware: 'auth',
    mixins: [helpers],
    data: () => ({
      submitted:false,
      error: new error(),
      prev: 1,
      imgSrc: '',
      cropImg: '',
      backgroundUri: '',
      posterUri: '',
      posterImgSrc:'',
      posterCropImg: '',


    }),

    methods: {
      validationEventSecondStep() {
       let _this = this
        let formData = new FormData();
        formData.append('name', this.eventData.event.name);
        formData.append('categoryId',this.eventData.event.categoryId);
        formData.append('venue', this.eventData.event.venue);
        formData.append('streetAdress',this.eventData.event.streetAdress);
        formData.append('city', this.eventData.event.city);
        formData.append('description', this.eventData.event.description);
        formData.append('cost', this.eventData.event.cost);
        formData.append('startDate', this.eventData.event.startDate);
        formData.append('endDate', this.eventData.event.endDate);
        formData.append('backgroundUri', this.backgroundUri);
        formData.append('posterUri', this.posterUri);
        formData.append('latitude', this.eventData.event.latitude);
        formData.append('longitude', this.eventData.event.longitude);
          axios.post(apiRoute + '/user/profiles/'+this.eventData.event.profileId+'/events', formData, this.$store.getters['auth/token']).then(response => {
            this.$router.push({ path: this.prevRoute})
          }).catch(error => {
            _this.error.record(error.response.data.error)
          })
      },


      back() {
        this.$emit('validationEventStep', {
           step:this.prev,
           eventData: {
             event:this.eventData.event,
             imgSrc: this.imgSrc,
             cropImg:this.cropImg,
             backgroundUri: this.backgroundUri,
             posterUri: this.posterUri,
             posterImgSrc: this.posterImgSrc,
             posterCropImg: this.posterCropImg
           }
        });
      },


      setImage(e) {
        const file = e.target.files[0];
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
            if(this.$refs.posterCropperImage != undefined) {
              this.$refs.posterCropperImage.replace(event.target.result);
            }
          };
          reader.readAsDataURL(file);
        } else {
          alert("Sorry, FileReader API not supported");
        }
      },


      cropPosterImage() {
        this.posterCropImg = this.$refs.posterCropperImage.getCroppedCanvas().toDataURL();
        $("#eventPosterModal").modal('hide');
        this.posterUri = this.dataURLtoFile(this.posterCropImg, 'file');
      },



    },

    created() {

    },

    mounted() {
      if(this.eventData != '' && this.eventData != undefined) {
          this.imgSrc = this.eventData.imgSrc,
          this.cropImg = this.eventData.cropImg,
          this.backgroundUri =  this.eventData.backgroundUri,
          this.posterUri =  this.eventData.posterUri,
          this.posterImgSrc = this.eventData.posterImgSrc,
          this.posterCropImg = this.eventData.posterCropImg
      }
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


  .error-message {
    color: red;
  }

</style>
