<template>
  <div class="my-favourite-creators">
    <!-- Tiles-->
    <TilesList v-bind:tilesList="tilesList"></TilesList>
    <!--No followed creators-->
    <div class="empty-content-discover" v-if="!tilesList.length && !showLoad">
      <div class="line bolder">You do not have any followed creators yet!</div>

      <div class="line">Click "Discover" to search for creators to follow .</div>
      <button class="btn click-discover" v-on:click="$router.push({name: 'discover'})">Discover</button>
    </div>

    <div class="loading" v-show="showLoad">
      <span class="fa fa-spinner fa-spin"></span>
    </div>
  </div>
</template>

<script>
  import TilesList from "../../shared/tiles/TilesList.vue";
export default {
  name: "Creators",
  middleware: 'auth',
  components: {
    TilesList
  },
  data: function() {
    return {
      tilesList: [],
      showLoad: true,
    };
  },

  methods: {
    getFollowedProfiles() {
      let _this = this;
      axios.get(apiRoute + '/user/followers/profiles',  this.$store.getters['auth/token']).then(response => {
        _this.tilesList = response.data.data;
        _this.showLoad = false;
      }).catch(error => {
        _this.showLoad = false;
      });
    },
  },

  created() {
    bus.$on('refresh', (data) => {
      if(data){
        this.getFollowedProfiles()
      }
    })
  },
  mounted() {
    this.getFollowedProfiles();
  }
};
</script>
