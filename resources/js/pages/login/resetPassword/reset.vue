<template>
  <div>
    <!--Header Nav For Desktop-->
    <div class="container d-none d-md-block">
      <div class="row">
        <div class="col">
          <ul class="nav justify-content-end">
            <router-link class="nav-link login" :to="{ name: 'login' }">{{ $t('login') }}</router-link>
          </ul>
        </div>
      </div>
    </div>

    <!--Enter email form-->
    <div class="container mt-5">
      <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
          <div class="alert alert-success" v-if="messages">
            <strong>Your password was changed, log in to continue.</strong>
          </div>

          <form>
            <h4>Retrieve password</h4>
            <p>
              This page has appeared because you have forgotten your access password and wish to set a new one.
              <br />Please, enter the new password twice below.
            </p>

            <div class="form-group input-group">
              <input
                v-if="show"
                type="text"
                class="form-control"
                id="password"
                placeholder="New password"
                v-validate="'required'"
                name="password"
                v-model="password"
                @keyup="validatePassword"
              />
              <input
                v-else
                type="password"
                class="form-control"
                id="passwordHide"
                placeholder="New password"
                v-validate="'required'"
                name="password"
                v-model="password"
                @keyup="validatePassword"
              />
              <div class="input-group-append">
                <span @click="showPassword" class="input-group-text">
                  <i v-if="show" class="far fa-eye"></i>
                  <i v-else class="far fa-eye-slash"></i>
                </span>
              </div>
              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('password') && !passwordErrorMessage"
              >{{ errors.first('password') }}</span>
              <div class="invalid-feedback">{{ passwordErrorMessage }}</div>
            </div>

            <div class="form-group input-group">
              <input
                v-if="showRepeat"
                type="text"
                class="form-control"
                id="repeatPassword"
                placeholder="Repeat password"
                name="repeat password"
                v-validate="{ required : true,  confirmed: password}"
                v-model="password_confirmation"
              />
              <input
                v-else
                type="password"
                class="form-control"
                id="repeatPassword"
                placeholder="Repeat password"
                name="repeat password"
                v-validate="{ required : true,  confirmed: password}"
                v-model="password_confirmation"
              />
              <div class="input-group-append">
                <span @click="showRepeatPassword" class="input-group-text">
                  <i v-if="showRepeat" class="far fa-eye"></i>
                  <i v-else class="far fa-eye-slash"></i>
                </span>
              </div>

              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('repeat password')"
              >{{ errors.first('repeat password') }}</span>
              <span class="invalid-feedback" v-text="error.get('password_confirmation')"></span>
            </div>

            <vue-recaptcha
              @verify="verify"
              :sitekey="sitekey"
              @expired="onCaptchaExpired"
              ref="recaptcha"
            ></vue-recaptcha>
            <div
              class="invalid-feedback"
              v-if="submitted && pleaseTickRecaptcha"
            >Please tick recaptcha.</div>
            <div class="mt-3 d-block d-md-none">
              <router-link class="nav-link login" :to="{ name: 'login' }">{{ $t('login') }}</router-link>
            </div>
            <div class="buttons-area">
              <router-link class="cancel-link" :to="{ path: '/'}">
                <i class="fas fa-times"></i> Cancel
              </router-link>
              <a
                class="continue-button"
                @click="validatePasswordForm"
                style="color: #292929; cursor: pointer"
              >Reset password</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
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
  middleware: "guest",
  components: { VueRecaptcha },

  metaInfo() {
    return { title: this.$t("reset_password") };
  },

  data() {
    return {
      token: "",
      sitekey: window.config.recaptchaSiteKey,
      pleaseTickRecaptcha: false,
      submitted: false,
      password: "",
      password_confirmation: "",
      error: new error(),
      messages: false,
      passwordErrorMessage: "",
      show: false,
      showRepeat: false
    };
  },

  created() {
    this.token = this.$route.params.token;
  },

  methods: {
    showRepeatPassword() {
      this.showRepeat = !this.showRepeat;
    },
    showPassword() {
      this.show = !this.show;
    },
    verify(recaptchaToken) {
      this.captcha = recaptchaToken;
    },

    validatePasswordForm() {
      this.submitted = true;
      if (!this.captcha) {
        this.pleaseTickRecaptcha = true;
      }

      this.$validator.validate().then(valid => {
        if (valid && this.captcha) {
          this.changePassword();
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

    changePassword() {
      this.check = false;
      axios
        .post(apiRoute + "/user/password-reset", {
          token: this.token,
          recaptchaToken: this.captcha,
          password: this.password,
          password_confirmation: this.password_confirmation
        })
        .then(response => {
          this.$refs.recaptcha.reset();
          this.messages = true;
          this.pleaseTickRecaptcha = false;
        })
        .catch(error => {});
    },

    onCaptchaExpired() {
      this.$refs.recaptcha.reset();
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
.input-group-text {
  background: black;
  color: white;
  border: 1px solid black;
  border-bottom: 1px solid white;
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

.input-group-text {
  background: black;
  color: white;
  border: 1px solid black;
  border-bottom: 1px solid white;
}

.login {
  color: #fff;
  transition: 0.3s;
  font-size: 1rem;
  &:hover {
    color: #9d72ff;
    transition: 0.3s;
  }
}

@media (max-width: 500px) {
  .continue-button {
    padding: 8px 15px !important;
  }
}
</style>
