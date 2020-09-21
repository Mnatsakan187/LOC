<template>
  <div class="mt-5">
    <div class="profile-creation">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <!--Form begins-->
            <form class="register-creator-step-2">
              <div class="title" v-if="!profileId">Create your profile</div>
              <div class="title" v-else>Edit profile</div>
              <!--User name, creative title, icon block-->
              <div class="user-name-icon clearfix mt-4">
                <div class="user-icon" @click="showModal = true">
                  <i class="fas fa-user"></i>
                  <i class="fas fa-plus"></i>
                  <img v-if="cropImg" :src="cropImg" class="profile-picture-image" />
                </div>
                <div class="user-name">
                  <div class="form-group">
                    <label class="sr-only" for="creativeTitle">CREATIVE TITLE</label>
                    <input
                      type="text"
                      v-validate="'required'"
                      name="creative title"
                      v-model="creativeTitle"
                      class="form-control creative-title"
                      id="creativeTitle"
                      placeholder="Creative title"
                    />
                    <div
                      class="invalid-feedback"
                      v-if="submitted && errors.has('creative title')"
                    >{{ errors.first("creative title") }}</div>
                    <span class="invalid-feedback" v-text="error.get('creativeTitle')"></span>
                  </div>
                </div>
              </div>

              <!--User bio textarea-->
              <div class="form-group mt-5 mb-5">
                <label class="sr-only" for="taBio">
                  BIO
                  <span class="gray">(OPTIONAL)</span>
                </label>
                <ckeditor
                  id="taBio"
                  name="biography"
                  v-validate="'max:1000'"
                  v-model="biography"
                  :editor="editor"
                  :config="editorConfig"
                ></ckeditor>
                <span
                  class="invalid-feedback"
                  v-if="submitted && errors.has('biography')"
                >{{ errors.first('biography') }}</span>
              </div>

              <!-- Location area -->
              <div class="content-area mt-2">
                <div class="sub-title">LOCATION</div>
                <div class="form-group">
                  <label class="sr-only" for="userCity">City</label>
                  <v-select
                    v-validate="'required'"
                    class="register-select"
                    name="city"
                    v-model="location"
                    id="userCity"
                    :options="cities"
                    placeholder="Please, select city"
                  ></v-select>
                  <div
                    class="invalid-feedback"
                    v-if="submitted && errors.has('city')"
                  >{{ errors.first("city") }}</div>
                </div>
              </div>

              <!--Profile Cover block-->
              <div class="add-profile-cover mt-4" @click="showModalBgModal = true">
                <h5 v-if="!croupBackgroundUrl">Add a profile cover</h5>
                <p v-if="!croupBackgroundUrl">This image will be the cover of your profile</p>
                <i v-if="!croupBackgroundUrl" class="fas fa-camera"></i>
                <img v-if="croupBackgroundUrl" :src="croupBackgroundUrl" class="background-image" />
              </div>

              <div class="col-12 mt-3">
                <div>
                  <div class="social-media-block">
                    <div class="sub-title text-center">Your social media</div>
                    <div class="social-media-icons mt-5">
                      <div
                        class="social-media-icon open-modal"
                        data-name="tumblr"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: tumblr }"
                      >
                        <i class="fab fa-tumblr main-icon"></i>
                        <i v-if="tumblr" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Tumblr</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="twitter"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: twitter }"
                      >
                        <i class="fab fa-twitter main-icon"></i>
                        <i v-if="twitter" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Twitter</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="soundcloud"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: soundcloud }"
                      >
                        <i class="fab fa-soundcloud main-icon"></i>
                        <i v-if="soundcloud" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Soundcloud</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="spotify"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: spotify }"
                      >
                        <i class="fab fa-spotify main-icon"></i>
                        <i v-if="spotify" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Spotify</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="youtube"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: youtube }"
                      >
                        <i class="fab fa-youtube main-icon"></i>
                        <i v-if="youtube" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Youtube</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="vimeo"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: vimeo }"
                      >
                        <i class="fab fa-vimeo-v main-icon"></i>
                        <i v-if="vimeo" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Vimeo</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="behance"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: behance }"
                      >
                        <i class="fab fa-behance main-icon"></i>
                        <i v-if="behance" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Behance</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="linkedin"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: linkedin }"
                      >
                        <i class="fab fa-linkedin-in main-icon"></i>
                        <i v-if="linkedin" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Linedin</span>
                      </div>

                      <div
                        class="social-media-icon open-modal"
                        data-name="etsy"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: etsy }"
                      >
                        <i class="fab fa-etsy main-icon"></i>
                        <i v-if="etsy" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Etsy</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="facebook"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: facebook }"
                      >
                        <i class="fab fa-facebook-f main-icon"></i>
                        <i v-if="facebook" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Facebook</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="instagram"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: instagram }"
                      >
                        <i class="fab fa-instagram main-icon"></i>
                        <i v-if="instagram" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Instagram</span>
                      </div>
                      <div
                        class="social-media-icon open-modal"
                        data-name="snapchat"
                        @click="showSocialModal = true"
                        v-bind:class="{ added: snapchat }"
                      >
                        <i class="fab fa-snapchat-ghost main-icon"></i>
                        <i v-if="snapchat" class="fas fa-check"></i>
                        <i v-else class="fas fa-plus"></i>
                        <span>Snapchat</span>
                      </div>
                    </div>
                  </div>
                  <ModalAddSocialMedia v-show="showSocialModal"></ModalAddSocialMedia>
                </div>
              </div>
            </form>
            <!--Form ends-->
            <ModalUploadImage
              v-on:images="getImages($event)"
              :imgSrcCrouped="cropImg"
              :cropper="cropper"
              v-if="showModal"
              @close="showModal = false"
            ></ModalUploadImage>
            <ModalUploadImage
              v-on:bgImages="getBgImages($event)"
              :backgroundSrcCrouped="croupBackgroundUrl"
              :cropper="cropperBg"
              v-if="showModalBgModal"
              @close="showModalBgModal = false"
            ></ModalUploadImage>
          </div>
        </div>
      </div>
    </div>

    <div class="row create-gray">
      <div class="col-sm-12 col-md-6 offset-md-3">
        <div class="action-buttons">
          <router-link class="btn cancel" :to="{ name: 'profile.index' }">
            <i class="fas fa-times"></i> Cancel
          </router-link>
          <button
            v-if="!profileId"
            type="button"
            @click="validationAddProfiles"
            class="btn save"
          >Add</button>
          <button v-else type="button" @click="validationAddProfiles" class="btn save">Edit</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueCropper from "vue-cropperjs";
import Cookies from "js-cookie";
import { helpers } from "../../mixins/helpers";
import "vue-select/dist/vue-select.css";
import ModalUploadImage from "../../shared/modals/ModalUploadImage";
import ModalAddSocialMedia from "../../shared/modals/ModalAddSocialMedia";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

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

export default {
  name: "Form.vue",
  components: { ModalAddSocialMedia, ModalUploadImage, VueCropper },
  mixins: [helpers],
  middleware: "auth",
  data() {
    return {
      submitted: false,
      creativeTitle: "",
      location: "",
      day: "",
      month: "",
      year: "",
      biography: "",
      months: "",
      years: "",
      error: new error(),
      currentYear: 0,
      daysInMonth: 31,
      profileImage: "",
      backgroundImage: "",
      urlProfileImage: "",
      urlBackgroundImage: "",
      dateOfBirth: "",
      token: Cookies.get("token"),
      imgSrc: "",
      cropImg: "",
      croupBackgroundUrl: "",
      image: "",
      croupBackground: "",
      cities: [],
      showModalBgModal: false,
      showModal: false,
      cropper: "cropper",
      cropperBg: "cropperBg",
      socials: [],
      instagram: "",
      snapchat: "",
      etsy: "",
      linkedin: "",
      tumblr: "",
      twitter: "",
      soundcloud: "",
      spotify: "",
      youtube: "",
      vimeo: "",
      behance: "",
      facebook: "",
      socialLink: "",
      showSocialModal: false,
      profileId: "",
      editor: ClassicEditor,
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
        placeholder: "Write a short bio...",
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
        }
      }
    };
  },

  methods: {
    validationAddProfiles() {
      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (valid) {
          if (this.profileId == undefined) {
            let formData = new FormData();
            formData.append("creativeTitle", this.creativeTitle);
            formData.append("location", this.location);
            formData.append("dateOfBirth", this.dateOfBirth);
            formData.append("biography", this.biography);
            formData.append("backgroundUri", this.backgroundImage);
            formData.append("avatarUri", this.profileImage);
            formData.append("socials", JSON.stringify(this.socials));
            this.submitted = true;

            axios
              .post(apiRoute + "/user/profiles", formData, this.token)
              .then(response => {
                this.$router.push({ name: "profile.index" });
              })
              .catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error);
              });
          } else {
            let formData = new FormData();
            formData.append("creativeTitle", this.creativeTitle);
            formData.append("location", this.location);
            formData.append("dateOfBirth", this.dateOfBirth);
            formData.append("biography", this.biography);
            formData.append("backgroundUri", this.backgroundImage);
            formData.append("avatarUri", this.profileImage);
            formData.append("socials", JSON.stringify(this.socials));
            this.submitted = true;
            this.$validator.validate().then(valid => {
              if (valid) {
                axios
                  .post(
                    apiRoute + "/user/profiles/update/" + this.profileId,
                    formData,
                    this.token
                  )
                  .then(response => {
                    this.$router.push({ name: "profile.index" });
                  })
                  .catch(error => {
                    let _this = this;
                    _this.error.record(error.response.data.error);
                  });
              }
            });
          }
        }
      });
    },

    getImages($event) {
      this.profileImage = $event.profileImage;
      this.cropImg = $event.cropImg;
    },

    getBgImages($event) {
      this.backgroundImage = $event.backgroundImage;
      this.croupBackgroundUrl = $event.croupBackgroundUrl;
    },

    back() {
      this.$emit("validationStep", this.prev);
    },

    getSocialLinks() {
      if (!this.errors.first("social media link")) {
        let data = {
          [$(".modal-body #socialName").val()]: this.socialLink
        };

        if ($(".modal-body #socialName").val() == "instagram") {
          this.instagram = true;
        } else if ($(".modal-body #socialName").val() == "snapchat") {
          this.snapchat = true;
        } else if ($(".modal-body #socialName").val() == "facebook") {
          this.facebook = true;
        } else if ($(".modal-body #socialName").val() == "etsy") {
          this.etsy = true;
        } else if ($(".modal-body #socialName").val() == "linkedin") {
          this.linkedin = true;
        } else if ($(".modal-body #socialName").val() == "tumblr") {
          this.tumblr = true;
        } else if ($(".modal-body #socialName").val() == "twitter") {
          this.twitter = true;
        } else if ($(".modal-body #socialName").val() == "soundcloud") {
          this.soundcloud = true;
        } else if ($(".modal-body #socialName").val() == "spotify") {
          this.spotify = true;
        } else if ($(".modal-body #socialName").val() == "youtube") {
          this.youtube = true;
        } else if ($(".modal-body #socialName").val() == "vimeo") {
          this.vimeo = true;
        } else if ($(".modal-body #socialName").val() == "behance") {
          this.behance = true;
        }

        this.socials.push(data);
        this.showSocialModal = false;
      }
    },

    getProfile() {
      let _this = this;
      axios
        .get(apiRoute + "/user/profiles/" + this.profileId, this.token)
        .then(response => {
          _this.profiles = response.data.data;

          this.creativeTitle = response.data.data.creativeTitle;
          this.location = response.data.data.location;
          this.dateOfBirth = response.data.data.dateOfBirth;
          this.biography = response.data.data.biography;

          if (response.data.data.socialMediaLinks) {
            response.data.data.socialMediaLinks.forEach((value, key) => {
              this.socials.push({
                [value.name]: value.socialMediaLink
              });
              if (value.name == "instagram") {
                this.instagram = true;
              } else if (value.name == "snapchat") {
                this.snapchat = true;
              } else if (value.name == "facebook") {
                this.facebook = true;
              } else if (value.name == "etsy") {
                this.etsy = true;
              } else if (value.name == "linkedin") {
                this.linkedin = true;
              } else if (value.name == "tumblr") {
                this.tumblr = true;
              } else if (value.name == "twitter") {
                this.twitter = true;
              } else if (value.name == "soundcloud") {
                this.soundcloud = true;
              } else if (value.name == "spotify") {
                this.spotify = true;
              } else if (value.name == "youtube") {
                this.youtube = true;
              } else if (value.name == "vimeo") {
                this.vimeo = true;
              } else if (value.name == "behance") {
                this.behance = true;
              }
            });
          }

          if (response.data.data.avatarUri) {
            _this.cropImg =
              "/storage/profiles/profilePictureImage/" +
              response.data.data.id +
              "/" +
              response.data.data.avatarUri;
          }
          if (response.data.data.backgroundUri) {
            _this.croupBackgroundUrl =
              "/storage/profiles/profileBackgroundImage/" +
              response.data.data.id +
              "/" +
              response.data.data.backgroundUri;
          }
        })
        .catch(error => {});
    }
  },

  mounted() {
    this.months = moment.months();
    this.cities = cities;

    let _this = this;
    $(document).on("click", ".open-modal", function() {
      let socialName = $(this).data("name");
      $(".modal-body #socialName").val(socialName);
      $("#socialMediaLink").val("");
      if (_this.socials) {
        _this.socials.forEach((value, key) => {
          if (socialName in value) {
            if (socialName == "instagram") {
              $("#socialMediaLink").val(value.instagram);
            } else if (socialName == "snapchat") {
              $("#socialMediaLink").val(value.snapchat);
            } else if (socialName == "facebook") {
              $("#socialMediaLink").val(value.facebook);
            } else if (socialName == "etsy") {
              $("#socialMediaLink").val(value.etsy);
            } else if (socialName == "linkedin") {
              $("#socialMediaLink").val(value.linkedin);
            } else if (socialName == "tumblr") {
              $("#socialMediaLink").val(value.tumblr);
            } else if (socialName == "twitter") {
              $("#socialMediaLink").val(value.twitter);
            } else if (socialName == "soundcloud") {
              $("#socialMediaLink").val(value.soundcloud);
            } else if (socialName == "spotify") {
              $("#socialMediaLink").val(value.spotify);
            } else if (socialName == "youtube") {
              $("#socialMediaLink").val(value.youtube);
            } else if (socialName == "vimeo") {
              $("#socialMediaLink").val(value.vimeo);
            } else if (socialName == "behance") {
              $("#socialMediaLink").val(value.behance);
            }
          }
        });
      }
    });
  },

  created() {
    this.currentYear = moment().year();
    bus.$on("close", data => {
      if (data == 1) {
        this.showSocialModal = false;
      }
    });

    bus.$on("save", data => {
      if (data) {
        this.socialLink = data.socialLink;
        this.getSocialLinks();
      }
    });

    this.profileId = this.$route.params.id;

    if (this.profileId) {
      this.cities = cities;
      this.months = moment.months();
      this.getProfile();
    }
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
</style>

<style scoped lang="scss">
.title {
  font-size: 2.5rem;
  font-family: EncodeSansSemiBold;
}
.sub-title {
  font-size: 1.5rem;
}
/* Social media icons styles */
.social-media-icons {
  display: flex;
  flex-flow: row wrap;
  justify-content: space-between;
}
.social-media-icon {
  width: 110px;
  height: 110px;
  text-align: center;
  position: relative;
  padding-top: 20px;
  margin-bottom: 10px;
  @media (max-width: 450px) {
    width: 100px;
    height: 100px;
  }
  .main-icon {
    color: #fff;
    font-size: 2.5rem;
    cursor: pointer;
    margin-bottom: 10px;
    @media (max-width: 450px) {
      font-size: 1.7rem;
    }
  }
  .fa-plus,
  .fa-check {
    position: absolute;
    top: 45px;
    right: 15px;
    cursor: pointer;
    color: #fff;
    background: #000;
    font-size: 0.6rem;
    border-radius: 50%;
    border: 2px solid #fff;
    display: inline-block;
    padding: 0.3rem 0.3rem;
    @media (max-width: 450px) {
      top: 35px;
      right: 15px;
    }
  }
  &.added {
    background-color: #2f2f2f;
    border-radius: 50%;
    .main-icon {
      color: #fff;
    }
    .fa-check {
      background-color: #2f2f2f;
    }
  }
  span {
    display: block;
  }
}
</style>

<style scoped lang="scss">
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
  @media (max-width: 600px) {
    vertical-align: bottom;
  }
  .modal-container {
    width: 600px;
    margin: 0px auto;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
    color: #323232;
    @media (max-width: 600px) {
      width: 100%;
    }
    .modal-header {
      border: none;
    }
    .heading {
      font-family: EncodeSansSemiBold;
      font-size: 1.4rem;
    }
    input {
      border: none;
      background-color: #e5e5e5;
    }
    .close-modal {
      font-size: 1.2rem;
    }
    .modal-footer {
      background-color: #474747;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 0;
      justify-content: flex-start;
      .cancel {
        color: #fff;
        font-size: 1.2rem;
        &:hover {
          color: #b9b9b9;
        }
      }
      .save {
        color: #474747;
        font-size: 1.2rem;
        text-decoration: none;
        background-color: #fff;
        padding: 10px 60px;
        border-radius: 30px;
        margin-left: 20px;
        &:hover {
          opacity: 0.7;
          transition: 0.3s;
        }
      }
    }
  }
}
/* Modal transition styles */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}

.marginTop {
  margin-bottom: 80px;
}

.cursorPointer {
  cursor: pointer;
}

.cursorPointerColor {
  color: #292929 !important;
  cursor: pointer;
}

@media (max-width: 500px) {
  .profile-picture-image {
    top: -19px;
    left: -35px;
    width: 150px;
    height: 128px;
  }
}

.action-buttons {
  background-color: #474747;
  text-align: center;
  padding-top: 20px;
  padding-bottom: 20px;
  .cancel {
    color: #fff;
    font-size: 1.2rem;
    &:hover {
      color: #b9b9b9;
    }
  }
  .save {
    color: #474747;
    font-size: 1.2rem;
    text-decoration: none;
    background-color: #fff;
    padding: 10px 60px;
    border-radius: 30px;
    margin-left: 20px;
    @media (max-width: 767px) {
      padding: 5px 10px;
    }
    &:hover {
      opacity: 0.7;
      transition: 0.3s;
    }
  }
}

.create-gray {
  background-color: #474747;
}

.create-gray {
  @media (max-width: 767px) {
    margin-bottom: 64px;
  }
}

.profile-cover-mb {
  margin-bottom: 80px;
}
</style>

<style>
.ck-editor__editable {
  background-color: #000 !important;
  color: #fff !important;
  border-bottom: 1px solid #fff !important;
  border-radius: 0px 0px 10px 10px !important;
  font-size: 1.2rem !important;
}

.ck-toolbar {
  border-radius: 10px 10px 0px 0px !important;
}
</style>
