<template>
  <div>
    <step-first
      v-on:validationStep="getStep($event)"
      v-if="step == 1"
      v-on:creator="getCreator($event)"
    ></step-first>
    <choose-plan
      v-on:validationStep="getStep($event)"
      v-if="step == 2"
      v-on:stripe="getStripe($event)"
      :creatorParam="creatorParam"
    ></choose-plan>
    <step-third
      v-on:validationStep="getStep($event)"
      v-if="step == 3"
      :creatorParam="creatorParam"
    ></step-third>
    <step-fourth
      v-on:validationStep="getStep($event)"
      v-if="step == 4"
      v-on:creator="getCreator($event)"
      :creatorParam="creatorParam"
      :stripe="stripe"
    ></step-fourth>
    <step-fifth
      v-on:validationStep="getStep($event)"
      v-if="step == 5"
      :creatorParam="creatorParam"
      :stripe="stripe"
    ></step-fifth>
    <step-sixth
      v-on:validationStep="getStep($event)"
      v-if="step == 6"
      :creatorParam="creatorParam"
    ></step-sixth>
  </div>
</template>

<script>
  import StepFirst from "./creatorRegisterSteps/step-first";
  import ChoosePlan from "./../../shared/payment/choose-plan";
  import StepThird from "./creatorRegisterSteps/step-third";
  import StepFourth from "./creatorRegisterSteps/step-fourth";
  import StepFifth from "./creatorRegisterSteps/step-fifth";
  import StepSixth from "./creatorRegisterSteps/step-sixth";

  export default {
    components: {
      StepFirst,
      ChoosePlan,
      StepThird,
      StepFourth,
      StepFifth,
      StepSixth
    },
    middleware: "guest",

    metaInfo() {
      return {title: this.$t("register")};
    },

    data() {
      return {
        step: 1,
        creatorParam: "",
        stripe: ""
      };
    },

    methods: {
      getStep: function ($event) {
        if ($event) {
          this.step = $event;
        }
      },

      getCreator($event) {
        this.creatorParam = $event;
      },

      getStripe($event) {
        this.stripe = $event;
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
