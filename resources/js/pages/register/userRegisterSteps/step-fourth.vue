<template>
  <div class="profile-creation">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
          <!-- Navigation for profile creation -->
          <div class="loc-navigation">
            <a class="btn">1 Interests</a>
            <a class="btn">2 Info</a>
            <a class="btn router-link-exact-active router-link-active">3 Social Media</a
            >
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
          <div class="social-media-block">
            <div class="title">Your social media</div>
            <div class="social-media-icons mt-5">
              <div
                class="social-media-icon open-modal"
                data-name="tumblr"
                @click="showModal = true"
                v-bind:class="{ added: user.tumblr }"
              >
                <i class="fab fa-tumblr main-icon"></i>
                <i v-if="user.tumblr" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Tumblr</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="twitter"
                @click="showModal = true"
                v-bind:class="{ added: user.twitter }"
              >
                <i class="fab fa-twitter main-icon"></i>
                <i v-if="user.twitter" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Twitter</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="soundcloud"
                @click="showModal = true"
                v-bind:class="{ added: user.soundcloud }"
              >
                <i class="fab fa-soundcloud main-icon"></i>
                <i v-if="user.soundcloud" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Soundcloud</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="spotify"
                @click="showModal = true"
                v-bind:class="{ added: user.spotify }"
              >
                <i class="fab fa-spotify main-icon"></i>
                <i v-if="user.spotify" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Spotify</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="youtube"
                @click="showModal = true"
                v-bind:class="{ added: user.youtube }"
              >
                <i class="fab fa-youtube main-icon"></i>
                <i v-if="user.youtube" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Youtube</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="vimeo"
                @click="showModal = true"
                v-bind:class="{ added: user.vimeo }"
              >
                <i class="fab fa-vimeo-v main-icon"></i>
                <i v-if="user.vimeo" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Vimeo</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="behance"
                @click="showModal = true"
                v-bind:class="{ added: user.behance }"
              >
                <i class="fab fa-behance main-icon"></i>
                <i v-if="user.behance" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Behance</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="linkedin"
                @click="showModal = true"
                v-bind:class="{ added: user.linkedin }"
              >
                <i class="fab fa-linkedin-in main-icon"></i>
                <i v-if="user.linkedin" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Linedin</span>
              </div>

              <div
                class="social-media-icon open-modal"
                data-name="etsy"
                @click="showModal = true"
                v-bind:class="{ added: user.etsy }"
              >
                <i class="fab fa-etsy main-icon"></i>
                <i v-if="user.etsy" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Etsy</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="facebook"
                @click="showModal = true"
                v-bind:class="{ added: user.facebook }"
              >
                <i class="fab fa-facebook-f main-icon"></i>
                <i v-if="user.facebook" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Facebook</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="instagram"
                @click="showModal = true"
                v-bind:class="{ added: user.instagram }"
              >
                <i class="fab fa-instagram main-icon"></i>
                <i v-if="user.instagram" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Instagram</span>
              </div>
              <div
                class="social-media-icon open-modal"
                data-name="snapchat"
                @click="showModal = true"
                v-bind:class="{ added: user.snapchat }"
              >
                <i class="fab fa-snapchat-ghost main-icon"></i>
                <i v-if="user.snapchat" class="fas fa-check"></i>
                <i v-else class="fas fa-plus"></i>
                <span>Snapchat</span>
              </div>
            </div>
          </div>

          <ModalAddSocialMedia v-show="showModal"></ModalAddSocialMedia>

          <!-- Buttons area -->
          <div class="buttons-area">
            <a class="cancel-link" @click="back">
              <i class="fas fa-arrow-left"></i> Back
            </a>
            <a
              class="continue-button cursor-pointer-color"
              @click="validationStepSocialMedia"
            >Finish</a
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import ModalAddSocialMedia from "../../../shared/modals/ModalAddSocialMedia";

  export default {
    name: "step-fourth",
    middleware: "guest",
    components: {ModalAddSocialMedia},
    props: ["userParam"],

    data() {
      return {
        next: 5,
        prev: 3,
        socialLink: "",
        socialName: "",
        user: {
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
          facebook: ""
        },
        submitted: false,
        showModal: false
      };
    },

    methods: {
      validationStepSocialMedia() {
        let formData = new FormData();
        formData.append("backgroundUri", this.userParam.profileData.profileBackgroundImage);
        formData.append("avatarUri", this.userParam.profileData.profileImage);
        formData.append("firstName", this.userParam.firstName);
        formData.append("lastName", this.userParam.lastName);
        formData.append("accountType", this.userParam.accountType);
        formData.append("preferredPronoun", this.userParam.displayName);
        formData.append("password", this.userParam.password);
        formData.append("password_confirmation", this.userParam.password_confirmation);
        formData.append("email", this.userParam.email);
        formData.append("location", this.userParam.location);
        formData.append("contentPreferenceWritten", this.userParam.selected.includes(1) ?  1 : 0);
        formData.append("contentPreferenceAudio", this.userParam.selected.includes(2) ?  1 : 0);
        formData.append("contentPreferenceVisual", this.userParam.selected.includes(3) ?  1 : 0);
        formData.append("contentPreferenceEvents", this.userParam.selected.includes(4) ?  1 : 0);
        formData.append("dateOfBirth", this.userParam.dateOfBirth);
        formData.append("biography", this.userParam.profileData.biography);
        formData.append("creativeTitle", this.userParam.profileData.creativeTitle);
        formData.append("socials", JSON.stringify(this.user.socials));


        console.log(this.userParam, 5145151, formData)

        axios.post(apiRoute + "/user", formData).then(response => {

        }).catch(error => {
            let _this = this;
            _this.error.record(error.response.data.error);
        });

        this.$emit("validationStep", this.next);
      },

      back() {
        this.$emit("user", this.user);
        this.$emit("validationStep", this.prev);
      },

      nextTo(step) {
        this.$emit("validationStep", step);
      },

      getSocialLinks() {
        this.$validator.validate().then(valid => {
          if (valid) {
            let data = {
              [$(".modal-body #socialName").val()]: this.socialLink
            };

            if ($(".modal-body #socialName").val() == "instagram") {
              this.user.instagram = true;
            } else if ($(".modal-body #socialName").val() == "snapchat") {
              this.user.snapchat = true;
            } else if ($(".modal-body #socialName").val() == "facebook") {
              this.user.facebook = true;
            } else if ($(".modal-body #socialName").val() == "etsy") {
              this.user.etsy = true;
            } else if ($(".modal-body #socialName").val() == "linkedin") {
              this.user.linkedin = true;
            } else if ($(".modal-body #socialName").val() == "tumblr") {
              this.user.tumblr = true;
            } else if ($(".modal-body #socialName").val() == "twitter") {
              this.user.twitter = true;
            } else if ($(".modal-body #socialName").val() == "soundcloud") {
              this.user.soundcloud = true;
            } else if ($(".modal-body #socialName").val() == "spotify") {
              this.user.spotify = true;
            } else if ($(".modal-body #socialName").val() == "youtube") {
              this.user.youtube = true;
            } else if ($(".modal-body #socialName").val() == "vimeo") {
              this.user.vimeo = true;
            } else if ($(".modal-body #socialName").val() == "behance") {
              this.user.behance = true;
            }

            this.user.socials.push(data);
            this.showModal = false;
          }
        });
      }
    },

    created() {
      bus.$on("close", data => {
        if (data == 1) {
          this.showModal = false;
        }
      });

      bus.$on("save", data => {
        if (data) {
          this.socialLink = data.socialLink;
          this.getSocialLinks();
        }
      });
    },

    mounted() {
      if (this.userParam) {
        this.user = this.userParam;
      }
      let _this = this;
      $(document).on("click", ".open-modal", function () {
        let socialName = $(this).data("name");
        $(".modal-body #socialName").val(socialName);
        $("#socialMediaLink").val("");
        if (_this.user.socials) {
          _this.user.socials.forEach((value, key) => {
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
    }
  };
</script>

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
</style>
