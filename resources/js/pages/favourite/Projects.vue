<template>
  <div class="my-favourite-projects">
    <!-- Tiles-->
    <TilesList v-bind:tilesList="tilesList"></TilesList>
    <!--No liked projects-->
    <div class="empty-content-discover" v-if="!tilesList.length && !showLoad">
      <div class="line bolder">You did not like any projects yet!</div>

      <div class="line">
        Click "Discover" to search for diffrent projects,
        and maybe you will like something.
      </div>
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
  name: "MyFavouriteProjects",
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
    getFavProjects() {
      let _this = this;
      axios.get(apiRoute + '/user/get/favourite/projects',  this.$store.getters['auth/token']).then(response => {
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
        this.getFavProjects()
      }
    })

    bus.$on('like', (data) => {
      if(data == 1 ){
        this.getFavProjects(1)
      }
    })


  },
  mounted() {
    this.getFavProjects();
  }
};
</script>

<style>
  @media (max-width: 500px) {
    .line{
      padding-left: 10px;
      padding-right: 10px;
    }
  }

</style>
