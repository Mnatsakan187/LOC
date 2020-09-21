<template>
  <div class="container">
    <div class="row mt-5">
      <div class="col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3">
        <div class="title">Welcome to LOC</div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3">
        <div class="sub-title">Create your account</div>

        <!--Register Form Begins-->
        <form
          class="register-creator-step-a"
          onsubmit="return false"
          novalidate
        >
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
            >
              {{ errors.first("first name") }}
            </div>
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
              placeholder="Last name"
              required
            />
            <div
              class="invalid-feedback"
              v-if="submitted && errors.has('last name')"
            >
              {{ errors.first("last name") }}
            </div>
            <div class="invalid-feedback" v-text="error.get('lastName')"></div>
          </div>

          <!-- Enter Email or Phone Number Input -->
          <div class="form-group">
            <label class="sr-only" for="emailPhone"
              >Email or Phone Number</label
            >
            <input
              v-model="user.email"
              v-validate="'required|email'"
              name="email"
              type="text"
              class="form-control"
              id="emailPhone"
              placeholder="Email or phone number"
              required
            />
            <div
              class="invalid-feedback"
              v-if="submitted && errors.has('email')"
            >
              {{ errors.first("email") }}
            </div>
            <div class="invalid-feedback" v-if="error.get('email') && !errors.first('email')">{{error.get('email')}}</div>
          </div>
          <!-- Enter Password Input -->
          <div class="form-group mt-4 input-group">
            <label class="sr-only" for="password">Password</label>
            <input
              v-if="show"
              v-model="user.password"
              type="text"
              name="password"
              class="form-control"
              placeholder="Password"
              v-validate="'required'"
              @keyup="validatePassword"
              required
              id="password"
              autocomplete="on"
            />
            <input
              v-else
              v-model="user.password"
              type="password"
              name="password"
              class="form-control"
              id="password"
              placeholder="Password"
              v-validate="'required'"
              @keyup="validatePassword"
              required
              autocomplete="on"
            />
            <div class="input-group-append">
              <span @click="showPassword" class="input-group-text">
                <i v-if="show" class="far fa-eye"></i>
                <i v-else class="far fa-eye-slash"></i>
              </span>
            </div>
            <div class="invalid-feedback">{{ passwordErrorMessage }}</div>
          </div>
          <!--Checkbox Terms and Conditions-->
          <div class="form-group form-check mb-4">
            <label class="form-check-label" for="cbTermsAndConditions">
            <input
              v-model="user.agree"
              name="agree"
              v-validate="'required'"
              type="checkbox"
              class="form-check-input"
              id="cbTermsAndConditions"
              required
            />I agree with Terms &amp; Conditions</label
            >
            <div
              class="invalid-feedback"
              v-if="submitted && errors.has('agree')"
            >
              You must agree before submitting.
            </div>
          </div>
          <div class="vueRecaptcha">
            <vue-recaptcha
              @verify="verify"
              :sitekey="sitekey"
              @expired="onCaptchaExpired"
              ref="recaptcha"
            >
            </vue-recaptcha>
          </div>

          <div class="invalid-feedback" v-if="submitted && pleaseTickRecaptcha">
            Please tick recaptcha.
          </div>
          <!--Submit and cancel buttons block-->
          <div class="buttons-area">
            <router-link class="cancel-link" to="/">
              <i class="fas fa-times"></i> Cancel
            </router-link>
            <a
              class="continue-button cursor-pointer-color"
              @click="validateUserForm"
              >Continue</a
            >
          </div>
        </form>
        <!--Register Form Ends-->
      </div>
    </div>
  </div>
</template>

<script>
import LocaleDropdown from "../../../shared/LocaleDropdown";
import VueRecaptcha from "vue-recaptcha";

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
  name: "step-first",
  middleware: "guest",
  props: ["userParam"],
  components: { LocaleDropdown, VueRecaptcha },
  data() {
    return {
      user: {
        firstName: "",
        lastName: "",
        accountType: 0,
        displayName: "",
        password: "",
        password_confirmation: "",
        preferredPronoun: "",
        agree: false,
        email: "",
        location: "",
        contentPreferenceWritten: 0,
        contentPreferenceAudio: 0,
        contentPreferenceVisual: 0,
        contentPreferenceEvents: 0,
        selected: [],
        dateOfBirth: "",
        day: "",
        month: "",
        year: "",
        cropImg: "",
        croupBackgroundUrl: "",
        socials: [],
        instagram: "",
        snapchat: "",
        etsy: "",
        linkedin: "",
        tumblr: "",
        twitter: "",
        soundcloud: "",
        square: "",
        youtube: "",
        vimeo: "",
        behance: "",
        facebook: "",
        profileData: {
          biography: "",
          creativeTitle: "",
          profileBackgroundImage: "",
          profilePictureImage: ""
        }
      },
      mustVerifyEmail: false,
      remember: false,
      error: new error(),
      messages: "",
      next: 2,
      submitted: false,
      sitekey: window.config.recaptchaSiteKey,
      pleaseTickRecaptcha: false,
      captcha: "",
      validate: false,
      show: false,
      passwordErrorMessage: "",
      validaPass: false
    };
  },

  methods: {
    verify(recaptchaToken) {
      this.captcha = recaptchaToken;
      this.pleaseTickRecaptcha = false;
    },

    validateUserForm() {
      this.submitted = true;
      var p = document.getElementById("password").value;
      if (p == "") {
        this.passwordErrorMessage = "The password field is required.";
      }

      if (!this.captcha) {
        this.pleaseTickRecaptcha = true;
      }

      this.$validator.validate().then(valid => {
        if (valid && this.captcha && this.validaPass) {
          this.registerUser();
        }
      });
    },




    validatePassword() {
      var p = document.getElementById("password").value;
      if (p.length < 6 && p.search(/[a-z]/i) < 0 && p.search(/[0-9]/) < 0) {
        this.passwordErrorMessage =
          "Your password must be at least 6 characters, one letter, and one number";
      } else if (p.length < 6 && p.search(/[a-z]/i) < 0) {
        this.passwordErrorMessage =
          "Your password must be at least 6 characters and one letter";
      } else if (p.length < 6 && p.search(/[0-9]/) < 0) {
        this.passwordErrorMessage =
          "Your password must be at least 6 characters and one number";
      } else if (
        p.length > 6 &&
        p.search(/[a-z]/i) < 0 &&
        p.search(/[0-9]/) < 0
      ) {
        this.passwordErrorMessage =
          "Your password must have one letter, and one number";
      } else if ((p.length > 6 || p.length == 6) && p.search(/[0-9]/) < 0) {
        this.passwordErrorMessage = "Your password must have one number";
      } else if ((p.length > 6 || p.length == 6) && p.search(/[a-z]/i) < 0) {
        this.passwordErrorMessage = "Your password must have one letter";
      } else if (p.length < 6) {
        this.passwordErrorMessage =
          "Your password must be at least 6 characters";
      } else {
        this.passwordErrorMessage = "";
        this.validaPass = true;
      }
    },

    showPassword() {
      this.show = !this.show;
    },

    registerUser() {
      if (this.captcha) {
        axios
          .post(apiRoute + "/user/validate/email", {
            email: this.user.email,
            recaptchaToken: this.captcha
          })
          .then(response => {
            let _this = this;
            _this.$emit("user", this.user);
            _this.$emit("validationStep", this.next);
          })
          .catch(error => {
            let _this = this;
            _this.error.record(error.response.data.error);
            _this.$refs.recaptcha.reset(), (_this.pleaseTickRecaptcha = false);
          });
      }
    },

    onCaptchaExpired() {
      this.$refs.recaptcha.reset();
    }
  },

  mounted() {
    if (this.userParam) {
      this.user = this.userParam;
      this.validaPass = true;
    }
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
.buttons-area {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #292929;
  padding: 20px;
  text-align: center;
  .cancel-link {
    color: #fff;
    text-decoration: none;
    font-size: 1.2rem;
    padding: 10px;
    transition: 0.3s;
    &:hover {
      color: #949494;
      transition: 0.3s;
    }
  }
  .continue-button {
    color: #292929;
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

//style for checkbox to be same level with label on mobile
.form-check {
  @media (max-width: 500px) {
    label {
      padding-left: 5px;
    }
  }
}

.input-group-text {
  background: black;
  color: white;
  border: 1px solid black;
  border-bottom: 1px solid white;
}

.form-check-input{
  position: relative;
  margin-right: 10px;
}

</style>
