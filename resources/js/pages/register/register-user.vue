<template>
  <div>
    <step-first
      v-on:validationStep="getStep($event)"
      v-on:user="getUser($event)"
      v-if="step == 1"
      :userParam="userParam"
    ></step-first>
    <step-second
      v-on:validationStep="getStep($event)"
      v-on:user="getUser($event)"
      v-if="step == 2"
      :userParam="userParam"
    ></step-second>
    <step-third
      v-on:validationStep="getStep($event)"
      v-on:user="getUser($event)"
      v-if="step == 3"
      :userParam="userParam"
    ></step-third>
    <step-fourth
      v-on:validationStep="getStep($event)"
      v-on:user="getUser($event)"
      v-if="step == 4"
      :userParam="userParam"
    ></step-fourth>
    <step-fifth
      v-on:validationStep="getStep($event)"
      v-if="step == 5"
      :userParam="userParam"
    ></step-fifth>
  </div>
</template>

<script>
import StepFirst from "./userRegisterSteps/step-first";
import StepSecond from "./userRegisterSteps/step-second";
import StepThird from "./userRegisterSteps/step-third";
import StepFourth from "./userRegisterSteps/step-fourth";
import StepFifth from "./userRegisterSteps/step-fifth";
export default {
  components: {
    StepFifth,
    StepFourth,
    StepThird,
    StepSecond,
    StepFirst
  },
  middleware: "guest",

  metaInfo() {
    return { title: this.$t("register") };
  },

  data() {
    return {
      step: 1,
      userParam: ""
    };
  },

  methods: {
    getStep($event) {
      if ($event) {
        this.step = $event;
      }
    },

    getUser($event) {
      this.userParam = $event;
    }
  }
};
</script>

<style lang="scss">
//Common buttons area styles
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

.loc-navigation a {
  cursor: pointer;
}
</style>
