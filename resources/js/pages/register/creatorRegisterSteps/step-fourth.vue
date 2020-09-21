<template>
  <div>
    <div class="profile-creation">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <!-- Navigation for profile creation -->
            <div class="loc-navigation">
              <a class="btn">1 Interests</a>
              <a class="btn router-link-exact-active router-link-active">2 Info</a>
              <a class="btn">3 Social Media</a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <!--Form begins-->
            <form class="register-creator-step-2">
              <div class="title">Create your profile</div>
              <!--User name, creative title, icon block-->
              <div class="user-name-icon clearfix mt-4">
                <div class="user-icon" @click="showModal = true">
                  <i class="fas fa-user"></i>
                  <i class="fas fa-plus"></i>
                  <img v-if="user.cropImg" :src="user.cropImg" class="profile-picture-image" />
                </div>
                <div class="user-name">
                  <div class="form-group">
                    <div class="name-surname">{{ user.firstName }} {{ user.lastName }}</div>
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="creativeTitle">CREATIVE TITLE</label>
                    <input
                      type="text"
                      v-validate="'required'"
                      name="creative title"
                      v-model="user.profileData.creativeTitle"
                      class="form-control creative-title"
                      id="creativeTitle"
                      placeholder="Creative title"
                    />
                    <div
                      class="invalid-feedback"
                      v-if="submitted && errors.has('creative title')"
                    >{{ errors.first("creative title") }}</div>
                  </div>
                </div>
              </div>

              <!--User bio textarea-->
              <div class="form-group mt-5 mb-5">
                <label class="sr-only" for="biography">
                  BIO
                  <span class="gray">(OPTIONAL)</span>
                </label>
                <ckeditor
                  id="biography"
                  name="biography"
                  v-validate="'max:1000'"
                  v-model="editorData"
                  :editor="editor"
                  :config="editorConfig"
                  @ready="prefill"
                ></ckeditor>
                <span
                  class="invalid-feedback"
                  v-if="submitted && errors.has('biography')"
                >{{ errors.first('biography') }}</span>
              </div>

              <!--User Birthday block-->
              <label class="birthday-label">Date of birth</label>
              <div class="form-row birthday">
                <div class="col-md-3">
                  <label for="bdDate" class="sr-only"></label>
                  <v-select
                    v-validate="'required'"
                    v-model="user.day"
                    name="day"
                    class="register-select"
                    id="bdDate"
                    :options="days"
                    placeholder="Day"
                  ></v-select>
                  <div
                    class="invalid-feedback"
                    v-if="submitted && errors.has('day')"
                  >{{ errors.first("day") }}</div>
                </div>
                <div class="col-md-6">
                  <label for="bdMonth" class="sr-only">Month</label>
                  <v-select
                    v-validate="'required'"
                    class="register-select"
                    name="month"
                    v-model="user.month"
                    id="bdMonth"
                    :options="months"
                    placeholder="Month"
                  ></v-select>
                  <div
                    class="invalid-feedback"
                    v-if="submitted && errors.has('month')"
                  >{{ errors.first("month") }}</div>
                </div>
                <div class="col-md-3">
                  <label for="bdYear" class="sr-only">Year</label>
                  <v-select
                    v-validate="'required'"
                    class="register-select"
                    name="year"
                    v-model="user.year"
                    id="bdYear"
                    :options="years"
                    placeholder="Year"
                  ></v-select>
                  <div
                    class="invalid-feedback"
                    v-if="submitted && errors.has('year')"
                  >{{ errors.first("year") }}</div>
                </div>
              </div>
              <!--Profile Cover block-->
              <div class="add-profile-cover profile-cover-mb mt-4" @click="showModalBgModal = true">
                <h5 v-if="!user.croupBackgroundUrl">Add a profile cover</h5>
                <p v-if="!user.croupBackgroundUrl">This image will be the cover of your profile</p>
                <i v-if="!user.croupBackgroundUrl" class="fas fa-camera"></i>
                <img
                  v-if="user.croupBackgroundUrl"
                  :src="user.croupBackgroundUrl"
                  class="background-image"
                />
              </div>
              <!-- Buttons area -->
              <div class="buttons-area">
                <a @click="back" class="cancel-link pointer">
                  <i class="fas fa-arrow-left"></i> Back
                </a>
                <a class="continue-button cursor-pointer-color" @click="validationStepInfo">Continue</a>
              </div>
            </form>
            <!--Form ends-->
            <ModalUploadImage
              v-on:images="getImages($event)"
              :imgSrcCrouped="user.cropImg"
              :cropper="cropper"
              v-show="showModal"
              @close="showModal = false"
            ></ModalUploadImage>
            <ModalUploadImage
              v-on:bgImages="getBgImages($event)"
              :backgroundSrcCrouped="user.croupBackgroundUrl"
              :cropper="cropperBg"
              v-show="showModalBgModal"
              @close="showModalBgModal = false"
            ></ModalUploadImage>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { helpers } from "../../../mixins/helpers";
import "vue-select/dist/vue-select.css";
import ModalUploadImage from "../../../shared/modals/ModalUploadImage";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
export default {
  name: "step-info",
  middleware: "guest",
  components: { ModalUploadImage },
  mixins: [helpers],
  props: ["creatorParam", "stripe"],
  data() {
    return {
      next: 5,
      prev: 3,
      submitted: false,
      months: [],
      years: [],
      currentYear: 0,
      daysInMonth: 31,
      profileImage: "",
      backgroundImage: "",
      urlProfileImage: "",
      urlBackgroundImage: "",
      location: "",
      creativeTitle: "",
      imgSrc: "",
      cropImg: "",
      croupBackgroundUrl: "",
      cities: [],
      showModal: false,
      showModalBgModal: false,
      days: [],
      mouthNumber: 1,
      cropper: "cropper",
      cropperBg: "cropperBg",
      user: {
        cropImg: "",
        croupBackgroundUrl: "",
        day: "",
        month: "",
        year: "",
        dateOfBirth: "",
        profileData: {
          biography: "",
          creativeTitle: "",
          profileBackgroundImage: "",
          profilePictureImage: ""
        }
      },
      editor: ClassicEditor,
      editorData: "",
      editorConfig: {
        toolbar: [
          "heading",
          "|",
          "bold",
          "italic",
          "link",
          "bulletedList",
          "numberedList",
          "blockQuote"
        ],
        heading: {
          options: [
            {
              model: "paragraph",
              title: "Paragraph",
              class: "ck-heading_paragraph"
            },
            {
              model: "heading1",
              view: "h1",
              title: "Heading 1",
              class: "ck-heading_heading1"
            },
            {
              model: "heading2",
              view: "h2",
              title: "Heading 2",
              class: "ck-heading_heading2"
            },
            {
              model: "heading3",
              view: "h3",
              title: "Heading 3",
              class: "ck-heading_heading3"
            },
            {
              model: "heading4",
              view: "h4",
              title: "Heading 4",
              class: "ck-heading_heading4"
            }
          ]
        },
        placeholder: "Write a short bio..."
      }
    };
  },

  methods: {
    prefill(editor) {
      this.editorData = this.user.profileData.biography;
    },
    validationStepInfo() {
      var months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
      ];
      months.forEach((value, key) => {
        if (value == this.user.month) {
          this.mouthNumber = key + 1;
        }
      });

      let str = this.user.day + "-" + this.mouthNumber + "-" + this.user.year;
      this.user.dateOfBirth = str;

      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (valid) {
          this.user.profileData.biography = this.editorData;
          this.$emit("creator", this.user);
          this.$emit("validationStep", this.next);
        }
      });
    },

    getImages($event) {
      this.user.profileData.profilePictureImage = $event.profileImage;
      this.user.cropImg = $event.cropImg;
    },

    getBgImages($event) {
      this.user.profileData.profileBackgroundImage = $event.backgroundImage;
      this.user.croupBackgroundUrl = $event.croupBackgroundUrl;
    },

    getDays() {
      var now = new Date();
      var days = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
      for (var i = 1; i <= days; i++) {
        this.days.push(i);
      }
    },

    getYears() {
      for (var i = 1960; i <= this.currentYear; i++) {
        this.years.push(i);
      }
    },

    back() {
      this.$emit("creator", this.user);
      this.$emit("validationStep", this.prev);
    },

    nextTo(step) {
      this.$emit("validationStep", step);
    }
  },

  mounted() {
    this.months = moment.months();
    this.cities = cities;
    this.getDays();
    this.getYears();
    if (this.creatorParam) {
      this.user = this.creatorParam;
    }
  },

  created() {
    this.currentYear = moment().year();
  }
};
</script>

<style scoped lang="scss">
.title {
  font-size: 2.3rem;
  font-family: EncodeSansSemiBold;
}
/* Registration user name and user icon */
.user-name-icon {
  .user-icon {
    position: relative;
    float: left;
    border-radius: 50%;
    width: 110px;
    height: 110px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 15px;
    background-color: #fff;
    cursor: pointer;
    i.fa-user {
      color: #000;
      font-size: 1.5rem;
    }
    i.fa-plus {
      position: absolute;
      top: 85px;
      right: 5px;
      color: #fff;
      background: #000;
      font-size: 1rem;
      border-radius: 50%;
      border: 2px solid #fff;
      display: inline-block;
      padding: 0.6rem 0.6rem;
      z-index: 1;
      @media (max-width: 500px) {
        padding: 0.4rem 0.4rem;
        top: 65px;
        right: 0px;
      }
    }
    @media (max-width: 500px) {
      width: 90px;
      height: 90px;
    }
  }
  .user-name {
    float: left;
    width: 70%;
    @media (max-width: 500px) {
      width: 60%;
    }
    .name-surname {
      font-family: EncodeSansSemiBold;
      font-size: 1.8rem;
    }
    .creative-title {
      background: #000;
      color: #fff;
      border: none;
      border-bottom: 1px solid #fff;
      border-radius: 0px;
      font-size: 1.2rem;
      &::placeholder {
        color: #fff;
        font-size: 1.3rem;
      }
    }
  }
}
.birthday-label {
  font-size: 1.5rem;
}

.bio-textarea {
  background: #000;
  color: #fff;
  border: 1px solid #fff;
  border-radius: 10px;
  font-size: 1.2rem;
  &::placeholder {
    color: #fff;
    font-size: 1.3rem;
  }
}

.add-profile-cover {
  position: relative;
  display: block;
  cursor: pointer;
  border: 1px solid #fff;
  border-radius: 0.25rem;
  padding: 20px 0;
  width: 100%;
  text-align: center;
  i.fa-camera {
    color: #fff;
    font-size: 2.5rem;
  }
}

.profile-picture-image {
  position: absolute;
  cursor: pointer;
  display: inline-block;
  border-radius: 50%;
  padding: 0.2em 0.3em;
  color: #ccc;
  font-size: 5rem;
  width: 160px;
  height: 144px;
  top: -17px;
  left: -25px;
}

.background-image {
  max-width: 100%;
  max-height: 155px;
}

@media (max-width: 500px) {
  .profile-picture-image {
    top: -19px;
    left: -35px;
    width: 150px;
    height: 128px;
  }

  .profile-cover-mb {
    margin-bottom: 0px !important;
  }
}

.profile-cover-mb {
  margin-bottom: 80px;
}
</style>

<style>
.ck-editor__editable {
  background: #000 !important;
  color: #fff !important;
  border-bottom: 1px solid #fff !important;
  border-radius: 0px 0px 10px 10px !important;
  font-size: 1.2rem !important;
}

.ck-toolbar {
  border-radius: 10px 10px 0px 0px !important;
}
</style>

