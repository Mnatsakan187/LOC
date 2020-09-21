<template>
  <div>
    <div class="profile-creation">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <!-- Navigation for profile creation -->
            <div class="loc-navigation">
              <a class="btn router-link-exact-active router-link-active">1 Interests</a>
              <a class="btn">2 Info</a>
              <a class="btn">3 Social Media</a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <!--Content Type area-->
            <div class="content-area mt-2">
              <div class="title">Tell us about you</div>
              <div class="sub-title">What kind of content are you interested in?</div>
              <div class="content-types">
                <div
                  class="type w"
                  @click="activate(1)"
                  :class="{ selected : user.selected.includes(1)}"
                >
                  <div class="circle">W</div>
                  <span>Written</span>
                </div>
                <div
                  class="type a"
                  @click="activate(2)"
                  :class="{ selected : user.selected.includes(2)}"
                >
                  <div class="circle">A</div>
                  <span>Audio</span>
                </div>
                <div
                  class="type v"
                  @click="activate(3)"
                  :class="{ selected : user.selected.includes(3)}"
                >
                  <div class="circle">V</div>
                  <span>Visual</span>
                </div>
                <div
                  class="type e"
                  @click="activate(4)"
                  :class="{ selected : user.selected.includes(4)}"
                >
                  <div class="circle">E</div>
                  <span>Events</span>
                </div>
              </div>
            </div>
            <!-- Location area -->
            <div class="content-area mt-2">
              <div class="sub-title">Where do you live?</div>
              <p>We will send you updated information happening near you.</p>
              <div class="form-group">
                <label class="sr-only" for="userCity">City</label>
                <v-select
                  v-validate="'required'"
                  class="register-select"
                  name="city"
                  v-model="user.location"
                  id="userCity"
                  :options="cities"
                  placeholder="Please, select city"
                ></v-select>
                <div
                  class="invalid-feedback"
                  v-if="submitted && errors.has('city')"
                >{{ errors.first('city') }}</div>
              </div>
            </div>
            <!-- Buttons area -->
            <div class="buttons-area">
              <a
                class="continue-button cursor-pointer-color"
                @click="validationStepInteres"
              >Continue</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "step-third",
  middleware: "guest",
  props: ["creatorParam", "stripe"],
  data() {
    return {
      next: 4,
      select: false,
      submitted: false,
      cities: [],
      user: {
        location: "",
        contentPreferenceWritten: 0,
        contentPreferenceAudio: 0,
        contentPreferenceVisual: 0,
        contentPreferenceEvents: 0,
        selected: []
      }
    };
  },

  methods: {
    validationStepInteres() {
      this.submitted = true;
      let _this = this;
      this.$validator.validate().then(valid => {
        if (valid) {
          _this.$emit("creator", this.user);
          _this.$emit("validationStep", _this.next);
        }
      });
    },

    activate(el) {
      this.select = el;
      if (this.user.selected.includes(el)) {
        this.user.selected.splice(this.user.selected.indexOf(el), 1);
      } else {
        this.user.selected.push(el);
      }
    },

    nextTo(step) {
      this.$emit("validationStep", step);
    }
  },
  mounted() {
    this.cities = cities;
    if (this.creatorParam) {
      this.user = this.creatorParam;
    }
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
</style>
