<template>
  <create-step-first v-if="eventStep == 1"  :prevRoute="prevRoute" :eventData="eventData" v-on:validationEventStep="getEventStep($event)"
                     v-on:project="getEvent($event)" ></create-step-first>
  <event-step-second v-else-if="eventStep == 2" v-on:validationEventStep="getEventStep($event)" :prevRoute="prevRoute" :eventData="eventData"></event-step-second>
</template>

<script>
  import CreateStepFirst from './event-step-first'
  import EventStepSecond from './event-step-second'
  export default {
    name: 'event-content',
    components: { EventStepSecond, CreateStepFirst },
    middleware: 'auth',
    data: () => ({
     prevRoute: '',
     eventData: '' ,
     eventStep: 1

    }),

    beforeRouteEnter(to, from, next) {
      next(vm => {
        if(from.path != '/') {
          vm.prevRoute = from.path;
        }else{
          vm.prevRoute = '/home';
        }
      })
    },

    methods: {
      getEventStep($event) {
        this.eventStep = $event.step;
        this.eventData = $event.eventData;
      },

      getEvent($event) {
        this.eventData = $event;
      },
    },

    created() {

    },

    mounted() {

    }
  }
</script>

<style scoped>

</style>
