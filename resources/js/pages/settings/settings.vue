<template>
  <div>
    <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3">
          <div class="sub-title">Update your account settings</div>

          <div class="alert alert-success" v-if="messages">
            <strong>Save was successful</strong>
          </div>
          <!--Register Form Begins-->
          <form class="register-creator-step-a" onsubmit="return false" novalidate>
            <!--User name, creative title, icon block-->
            <div class="user-name-icon clearfix mt-4 mb-4">
              <div class="user-icon" @click="showModal = true">
                <i class="fas fa-user"></i>
                <i class="fas fa-plus"></i>
                <img v-if="cropImg" :src="cropImg" class="profile-picture-image" />
              </div>
            </div>
            <!-- Enter First Name Input -->
            <div class="form-group">
              <label class="sr-only" for="firstName">First Name</label>
              <input
                v-validate="'required'"
                v-model="user.firstName"
                name="first name"
                type="text"
                class="form-control"
                id="firstName"
                placeholder="First name"
                required
              />
              <div
                class="invalid-feedback"
                v-if="submitted && errors.has('first name')"
              >Please enter first name.</div>
              <div class="invalid-feedback" v-text="error.get('firstName')"></div>
            </div>
            <!-- Enter Last Name Input -->
            <div class="form-group">
              <label class="sr-only" for="lastName">Last Name</label>
              <input
                v-validate="'required'"
                v-model="user.lastName"
                type="text"
                class="form-control"
                id="lastName"
                name="last name"
                placeholder="Enter last name"
                required
              />
              <div
                class="invalid-feedback"
                v-if="submitted && errors.has('last name')"
              >Please enter last name.</div>
              <div class="invalid-feedback" v-text="error.get('lastName')"></div>
            </div>

            <!-- Enter Email or Phone Number Input -->
            <div class="form-group">
              <label class="sr-only" for="emailPhone">Email or Phone Number</label>
              <input
                v-model="user.email"
                v-validate="'required|email'"
                name="email"
                type="email"
                class="form-control"
                id="emailPhone"
                placeholder="Enter email or phone number"
                required
              />
              <div
                class="invalid-feedback"
                v-if="submitted && errors.has('email')"
              >{{ errors.first('email') }}</div>
              <div
                class="invalid-feedback"
                v-if="error.get('email') && !errors.first('email')"
              >{{error.get('email')}}</div>
            </div>
            <button @click="openPasswords" class="btn click-discover mb-5">Change Password</button>

            <!-- Enter Password Input -->
            <div class="form-group mt-4" v-if="isActive">
              <input
                v-model="data.password"
                :class="{ 'is-invalid': submitted && errors.has('password') }"
                type="password"
                class="form-control"
                id="password"
                name="password"
                v-bind:placeholder="$t('enter_password')"
                v-validate="'required|min:6'"
              />
              <span
                class="error-message"
                v-if="submitted && errors.has('password')"
              >{{ errors.first('password') }}</span>
              <span class="error-message" v-if="pass" v-text="error.get('password')"></span>
            </div>

            <!-- Repeat Password Input -->
            <div class="form-group" v-if="isActive">
              <input
                v-model="data.password_confirmation"
                :class="{ 'is-invalid':submitted && errors.has('repeat password')}"
                type="password"
                class="form-control"
                id="repeatPassword"
                name="repeat password"
                placeholder="Repeat password"
                v-validate="{ required : true,  confirmed: data.password}"
              />
              <span
                class="error-message"
                v-if="submitted && errors.has('repeat password')"
              >{{ errors.first('repeat password') }}</span>
              <span class="error-message" v-if="pass" v-text="error.get('password_confirmation')"></span>
            </div>
            <div style="height: 100px"></div>
            <div class="col-12 mb-5">
              <!--Content Type area-->
              <div class="content-area mt-2">
                <div class="sub-title">What kind of content are you interested in?</div>
                <div class="content-types">
                  <div
                    class="type w"
                    @click="activate(1)"
                    :class="{ selected : selected.includes(1)}"
                  >
                    <div class="circle">W</div>
                    <span>Written</span>
                  </div>
                  <div
                    class="type a"
                    @click="activate(2)"
                    :class="{ selected : selected.includes(2)}"
                  >
                    <div class="circle">A</div>
                    <span>Audio</span>
                  </div>
                  <div
                    class="type v"
                    @click="activate(3)"
                    :class="{ selected : selected.includes(3)}"
                  >
                    <div class="circle">V</div>
                    <span>Visual</span>
                  </div>
                  <div
                    class="type e"
                    @click="activate(4)"
                    :class="{ selected : selected.includes(4)}"
                  >
                    <div class="circle">E</div>
                    <span>Events</span>
                  </div>
                </div>
              </div>
            </div>

            <!--Submit and cancel buttons block-->
            <!--<div class="buttons-area">-->
            <!--<router-link class="cancel-link" :to="{ path: prevRoute}">-->
            <!--<i class="fas fa-times"></i> Cancel-->
            <!--</router-link>-->
            <!--<a class="continue-button cursorPointerColor" @click="validateUserUpdateForm">Update</a>-->
            <!--</div>-->
          </form>
          <!--Register Form Ends-->
        </div>
        <ModalUploadImage
          v-on:images="getImages($event)"
          :imgSrcCrouped="cropImg"
          :cropper="cropper"
          v-if="showModal"
          @close="showModal = false"
        ></ModalUploadImage>
      </div>
    </div>

    <div class="row create-gray">
      <div class="col-sm-12 col-md-6 offset-md-3">
        <div class="action-buttons">
          <router-link class="btn cancel" :to="{ path: prevRoute}">
            <i class="fas fa-times"></i> Cancel
          </router-link>
          <button type="button" @click="validateUserUpdateForm" class="btn save">Update</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Cookies from "js-cookie";
import Navbar from "../../shared/Navbar";
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
import { helpers } from "../../mixins/helpers";
import ModalUploadImage from "../../shared/modals/ModalUploadImage";

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
  name: "settings.vue",
  components: { ModalUploadImage, Navbar, VueCropper },
  middleware: "auth",
  mixins: [helpers],
  data() {
    return {
      user: {
        firstName: "",
        lastName: "",
        accountType: "",
        preferredPronoun: "",
        agree: false,
        email: "",
        contentPreferenceWritten: "",
        contentPreferenceAudio: "",
        contentPreferenceVisual: "",
        contentPreferenceEvents: ""
      },
      submitted: false,
      isActive: false,
      error: new error(),
      token: Cookies.get("token"),
      selected: [],
      messages: "",
      urlImage: "",
      userImage: "",
      data: {
        password: "",
        password_confirmation: ""
      },
      pass: false,
      imgSrc: "",
      cropImg: "",
      image: "",
      showModal: false,
      cropper: "cropper",
      prevRoute: ""
    };
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (from.path != "/") {
        vm.prevRoute = from.path;
      } else {
        vm.prevRoute = "/profiles";
      }
    });
  },

  methods: {
    validateUserUpdateForm() {
      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (valid) {
          this.updateUser();
          this.updatePassword();
        }
      });
    },

    updateUser() {
      this.user.contentPreferenceWritten = this.selected.includes(1) ? 1 : 0;
      this.user.contentPreferenceAudio = this.selected.includes(2) ? 1 : 0;
      this.user.contentPreferenceVisual = this.selected.includes(3) ? 1 : 0;
      this.user.contentPreferenceEvents = this.selected.includes(4) ? 1 : 0;

      let formData = new FormData();
      formData.append("avatarUri", this.image);
      formData.append("firstName", this.user.firstName);
      formData.append("lastName", this.user.lastName);
      formData.append("preferredPronoun", this.user.preferredPronoun);
      formData.append("email", this.user.email);
      formData.append(
        "contentPreferenceWritten",
        this.user.contentPreferenceWritten
      );
      formData.append(
        "contentPreferenceAudio",
        this.user.contentPreferenceAudio
      );
      formData.append(
        "contentPreferenceVisual",
        this.user.contentPreferenceVisual
      );
      formData.append(
        "contentPreferenceEvents",
        this.user.contentPreferenceEvents
      );

      axios
        .post(apiRoute + "/user/update", formData, this.token)
        .then(response => {
          this.messages = true;
          bus.$emit("userData", response.data.data);
        })
        .catch(error => {
          let _this = this;
          _this.error.record(error.response.data.error);
        });
    },

    getUser() {
      axios
        .get(apiRoute + "/user", this.token)
        .then(response => {
          let _this = this;
          _this.user = response.data.data;

          if (response.data.data.contentPreferenceWritten != "") {
            _this.selected.push(1);
          }

          if (response.data.data.contentPreferenceAudio != "") {
            _this.selected.push(2);
          }

          if (response.data.data.contentPreferenceVisual != "") {
            _this.selected.push(3);
          }

          if (response.data.data.contentPreferenceEvents != "") {
            _this.selected.push(4);
          }

          if (response.data.data.avatarUri) {
            _this.cropImg =
              "/storage/avatarImage/" +
              response.data.data.id +
              "/" +
              response.data.data.avatarUri;
          }
        })
        .catch(error => {});
    },

    updatePassword() {
      if (this.data.password) {
        axios
          .put(apiRoute + "/user/change-password", this.data, this.token)
          .then(response => {})
          .catch(error => {
            let _this = this;
            _this.error.record(error.response.data.error);
            _this.pass = true;
          });
      }
    },

    activate(el) {
      this.select = el;
      if (this.selected.includes(el)) {
        this.selected.splice(this.selected.indexOf(el), 1);
      } else {
        this.selected.push(el);
      }
    },

    openPasswords() {
      this.isActive = !this.isActive;
      if (!this.isActive) {
        this.data.password = "";
        this.data.password_confirmation = "";
      }
    },

    getImages($event) {
      this.image = $event.profileImage;
      this.cropImg = $event.cropImg;
    }
  },

  created() {},

  mounted() {
    this.getUser();
    this.data.password = "";
    this.data.password_confirmation = "";
  }
};
</script>

<style scoped lang="scss">
.title {
  font-size: 3rem;
  font-family: EncodeSansSemiBold;
  margin-bottom: 40px;
  @media (max-width: 450px) {
    font-size: 2.5rem;
  }
}

.sub-title {
  font-size: 1.5rem;
  font-family: EncodeSansRegular;
  margin-bottom: 40px;
}

.form-group {
  margin-bottom: 40px;
  input {
    background-color: #000;
    border: none;
    border-radius: 0;
    border-bottom: 1px solid #fff;
    padding: 20px 0;
    color: #fff;
    font-size: 1.2rem;
    &::placeholder {
      color: #fff;
      font-size: 1.2rem;
    }
  }
  .invalid-feedback {
    color: #ff90fc;
    margin-top: 10px;
  }
}

//style for checkbox to be same level with label on mobile
.form-check {
  @media (max-width: 500px) {
    label {
      padding-top: 12px;
      padding-left: 5px;
    }
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

@media (max-width: 500px) {
  .profile-picture-image {
    top: -19px;
    left: -35px;
    width: 150px;
    height: 128px;
  }
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

.content-types {
  max-width: 400px;
  margin-top: 20px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
  display: flex;
  flex-flow: row wrap;
  justify-content: space-between;
  .type {
    width: 50%;
    display: block;
    margin-bottom: 30px;
    cursor: pointer;
    span {
      font-size: 1.2rem;
    }
    .circle {
      border-radius: 50%;
      width: 100px;
      height: 100px;
      font-weight: bold;
      font-size: 2.5rem;
      font-family: EncodeSansBold;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 10px auto;
    }
    &.w {
      color: #01ffc3;
      .circle {
        border: 1px solid #01ffc3;
      }
      &.selected {
        .circle {
          background-color: #01ffc3;
          color: #000;
        }
      }
    }
    &.a {
      color: #ff90fc;
      .circle {
        border: 1px solid #ff90fc;
      }
      &.selected {
        .circle {
          background-color: #ff90fc;
          color: #000;
        }
      }
    }
    &.v {
      color: #01aeff;
      .circle {
        border: 1px solid #01aeff;
      }
      &.selected {
        .circle {
          background-color: #01aeff;
          color: #000;
        }
      }
    }
    &.e {
      color: #9d72ff;
      .circle {
        border: 1px solid #9d72ff;
      }
      &.selected {
        .circle {
          background-color: #9d72ff;
          color: #000;
        }
      }
    }
  }
}

.cursorPointerColor {
  color: #292929 !important;
  cursor: pointer;
}

.click-discover {
  color: #fff;
  border: 2px solid #fff;
  border-radius: 10px;
  font-size: 1.4rem;
  transition: 0.5s;
  &:hover {
    color: #9d72ff;
    border: 2px solid #9d72ff;
    transition: 0.5s;
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
</style>
