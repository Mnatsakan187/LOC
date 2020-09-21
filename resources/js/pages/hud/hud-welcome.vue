<template>
  <div class="welcome-message">
    <div class="line bolder">Welcome to LOC !</div>

    <div class="line">
      This is the HUD. Here you will see the content of the creators you
      follow.
    </div>

    <div class="line">Click discover and start following creators.</div>
    <button class="btn click-discover" v-on:click="$router.push('discover')">Discover</button>
  </div>
</template>

<script>
  export default {
    name: "hud-welcome",
    middleware: 'auth',
    data: () => ({

    }),

    methods: {

    },

    created() {

    },

    mounted() {
      axios.put(apiRoute + '/user/update/first-time-user',  this.$store.getters['auth/token']).then(response => {

      }).catch(error => {

      });
    }
  }
</script>

<style scoped lang="scss">
  .welcome-message {
    padding-top: 30px;
    text-align: center;
    .line {
      padding-bottom: 20px;
      color: #fff;
      font-family: EncodeSansRegular;
      font-size: 1.6rem;
      &.bolder {
        font-family: EncodeSansBold;
        font-size: 1.8rem;
      }
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
  }
</style>
