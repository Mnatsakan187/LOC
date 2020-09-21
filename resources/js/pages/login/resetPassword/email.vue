<template>
  <div class="row">
    <!--Header Nav For Desktop-->
    <div class="container d-md-block">
      <div class="row">
        <div class="col">
          <ul class="nav justify-content-end">
            <li class="nav-item">
              <router-link class="nav-link login" :to="{ name: 'login' }">{{ $t('login') }}</router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--Enter email form-->
    <div class="container mt-5">
      <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
          <div class="alert alert-success" v-if="messages">
            <strong>The reset email sent</strong>
          </div>
          <form>
            <h4>{{ $t('have_you_forgotten_your_password') }}</h4>
            <p>{{$t('send_you_an_email_telling')}}</p>
            <div class="form-group">
              <input
                v-validate="'required|email'"
                type="email"
                class="form-control"
                id="email"
                v-model="email"
                name="email"
                v-bind:placeholder="$t('enter_email')"
                required
              />
              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('email')"
              >{{ errors.first('email') }}</span>
              <div
                class="invalid-feedback"
                v-if="error.get('email') && !errors.first('email')"
              >{{error.get('email')}}</div>
            </div>

            <div class="buttons-area">
              <router-link class="cancel-link" :to="{ path: prevRoute}">
                <i class="fas fa-times"></i> Cancel
              </router-link>
              <a
                class="continue-button"
                @click="validateEmailForm"
                style="color: #292929; cursor: pointer"
              >Send Email</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Form from "vform";
import LocaleDropdown from "../../../shared/LocaleDropdown";

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
  components: { LocaleDropdown },
  middleware: "guest",

  metaInfo() {
    return { title: this.$t("reset_password") };
  },

  data() {
    return {
      submitted: false,
      email: "",
      error: new error(),
      messages: false,
      check: false,
      prevRoute: ""
    };
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (from.path != "/") {
        vm.prevRoute = from.path;
      } else {
        vm.prevRoute = "/";
      }
    });
  },

  methods: {
    validateEmailForm() {
      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (valid) {
          this.send();
        }
      });
    },

    send() {
      this.check = false;
      axios
        .post(apiRoute + "/user/send/reset/email", {
          email: this.email
        })
        .then(response => {
          this.messages = true;
        })
        .catch(error => {
          let _this = this;
          _this.error.record(error.response.data.error);
          _this.check = true;
        });
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
</style>
