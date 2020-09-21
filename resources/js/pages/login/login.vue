<template>
  <div class="container login">
    <div class="row">
      <div class="col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3">
        <div class="title">Log In</div>

        <!--Login Form Begins-->
        <form class="register-creator-step-a">
          <!-- Enter Email or Phone Number Input -->
          <div class="form-group">
            <label class="sr-only" for="emailPhone">Email or Phone Number</label>
            <input
              v-validate="'required|email'"
              v-model="user.email"
              type="email"
              class="form-control"
              id="emailPhone"
              name="email"
              placeholder="Email or phone number"
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

          <!-- Enter Password Input -->
          <div class="form-group mt-4 input-group">
            <label class="sr-only" for="loginPasswordShow">Password</label>

            <input
              v-if="show"
              placeholder="Password"
              v-validate="'required'"
              v-model="user.password"
              class="form-control"
              type="text"
              name="password"
              id="loginPasswordShow"
            />

            <input
              v-else
              v-validate="'required'"
              v-model="user.password"
              placeholder="Password"
              class="form-control"
              type="password"
              name="password"
              id="loginPasswordHide"
              autocomplete="on"
            />

            <div class="input-group-append">
              <span @click="showPassword" class="input-group-text">
                <i v-if="show" class="far fa-eye"></i>
                <i v-else class="far fa-eye-slash"></i>
              </span>
            </div>
            <div
              class="invalid-feedback"
              v-if="submitted && errors.has('password')"
            >{{ errors.first('password') }}</div>
            <span class="invalid-feedback" v-text="error.get('password')"></span>
          </div>
          <div class="form-group row">
            <div class="col-md-7 offset-md-3" style="text-align: center">
              <span class="invalid-feedback" v-text="error.get('verify_error')"></span>
            </div>
          </div>
          <div class="badge-container">
            <vue-recaptcha
              @verify="verify"
              :sitekey="sitekey"
              @expired="onCaptchaExpired"
              ref="recaptcha"
            ></vue-recaptcha>
          </div>
          <div class="invalid-feedback" v-if="submitted && pleaseTickRecaptcha">
            Please tick
            recaptcha.
          </div>

          <!-- Remember Me -->
          <div class="form-group row mt-2">
            <div class="col-6 col-md-7 col-lg-7 col-sm-7">
              <div class="form-group form-check">
                <input
                  type="checkbox"
                  class="form-check-input"
                  id="cbRememberMe"
                  v-model="remember"
                  name="remember"
                />
                <label class="form-check-label" for="cbRememberMe">Remember Me</label>
              </div>
            </div>
            <div class="col-6 col-md-5 col-lg-5 col-sm-5">
              <router-link
                :to="{ name: 'password.request' }"
                class="small ml-auto my-auto forgot-password"
              >{{ $t('forgot_password') }}</router-link>
            </div>
          </div>

          <!--Submit and cancel buttons block-->
          <div class="buttons-area">
            <router-link class="cancel-link" to="/">
              <i class="fas fa-times"></i> Cancel
            </router-link>
            <a
              @click="validateLoginForm"
              class="continue-button"
              style="color: #292929; cursor: pointer"
            >Log In</a>
          </div>
        </form>
        <!--Login Form Ends-->
      </div>
    </div>
  </div>
</template>

<script>
import VueRecaptcha from "vue-recaptcha";
import LocaleDropdown from "../../shared/LocaleDropdown";

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

import Cookies from "js-cookie";
export default {
  name: "login",
  components: { LocaleDropdown, VueRecaptcha },
  middleware: "guest",

  metaInfo() {
    return { title: this.$t("login") };
  },

  data() {
    return {
      user: {
        email: "",
        password: ""
      },
      remember: false,
      emailError: "",
      passwordError: "",
      error: new error(),
      verify_error: "",
      submitted: false,
      sitekey: window.config.recaptchaSiteKey,
      pleaseTickRecaptcha: false,
      captcha: "",
      show: false
    };
  },

  methods: {
    verify(recaptchaToken) {
      this.captcha = recaptchaToken;
      this.pleaseTickRecaptcha = false;
    },

    validateLoginForm() {
      this.submitted = true;
      if (!this.captcha) {
        this.pleaseTickRecaptcha = true;
      }
      this.$validator.validate().then(valid => {
        if (valid && this.captcha) {
          this.login();
        }
      });
    },

    showPassword() {
      this.show = !this.show;
    },

    login() {
      // Submit the form.
      axios
        .post(apiRoute + "/user/login", this.user)
        .then(response => {
          // Save the token.
          this.$store.dispatch("auth/saveToken", {
            token: response.headers.token,
            remember: this.remember
          });
          // Fetch the user.
          this.$store.dispatch("auth/fetchUser");
          // Redirect home.
          if (response.data.data.firstTimeUser == 0) {
            this.$router.push({ name: "home" });
          } else {
            this.$router.push({ name: "my.feed" });
          }
        })
        .catch(error => {
          let _this = this;
          _this.error.record(error.response.data.error);
        });
    },

    onCaptchaExpired() {
      this.$refs.recaptcha.reset();
    }
  },

  mounted() {
    if (window.config.tokenKey && window.config.tokenKey != null) {
      this.$store.dispatch("auth/saveToken", {
        token: window.config.tokenKey,
        remember: this.remember
      });
      this.$store.dispatch("auth/fetchUser");
      Cookies.remove("tokenKey");
      window.config.tokenKey = "";

      this.$router.push({ name: "home" });
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

.fab fa-facebook {
  background: #3b5998;
  color: white;
}

.fab {
  padding: 20px;
  font-size: 30px;
  width: 50px;
  text-align: center;
  text-decoration: none;
  margin: 5px 2px;
}

.social-buttons {
  text-align: center;
}

.fab fa-linkedin {
  background: #007bb5 !important;
  color: white;
}

//style for checkbox to be same level with label on mobile
.form-check {
  @media (max-width: 500px) {
    label {
      padding-left: 5px;
    }
  }
}

.forgot-password {
  color: #fff;
  transition: 0.3s;
  font-size: 1rem;
  text-decoration: none;
  &:hover {
    color: #9d72ff;
    transition: 0.3s;
  }
}

.input-group-text {
  background: black;
  color: white;
  border: 1px solid black;
  border-bottom: 1px solid white;
}
</style>
