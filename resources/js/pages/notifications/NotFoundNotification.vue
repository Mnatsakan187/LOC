<template>
  <div class="empty-content-discover">
    <div class="empty-content-discover">
      <div class="line bolder">Sorry, the page you are looking for may have <br> been moved, deleted or does not exist.</div>
      <button class="btn click-discover" v-on:click="$router.push('discover')">Discover</button>
    </div>
  </div>
</template>

<script>
    export default {
      name: "NotFoundNotification",
      middleware: 'auth',
      data: () => ({
        users: [],

      }),

      methods: {
        updateNotification(){
          axios.get(apiRoute + '/notifications/read/'+this.notificationId, this.$store.getters['auth/token']).then(response => {

          }).catch(error => {

          })
        }
      },

      created() {
        this.notificationId = this.$route.params.notificationId
      },

      mounted() {
        $(".dropdown-menu-notification").removeClass('show');
        if(this.notificationId){
          this.updateNotification();
          bus.$emit('notRefresh', 1)
        }

      }
    }
</script>

<style scoped>

</style>
