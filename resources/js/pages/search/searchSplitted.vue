<template>
  <div class="search-splitted">
    <!-- Your content part -->
    <span class="title">YOUR CONTENT</span>
    <ul class="search-results">
      <li v-for="search in searches">
        <router-link
          v-if="search.hudType == 'project'"
          :to="{ path: '/project-detail/project/'+search.id}"
        >
          <span v-if="search.hudType == 'project'" class="bigger">{{search.name}}</span>
          <span v-if="search.hudType == 'project'">Project</span>
        </router-link>

        <router-link v-else :to="{path: '/profile-detail/'+search.id}">
          <span v-if="search.hudType == 'profile'" class="bigger">{{search.creativeTitle}}</span>
          <span v-if="search.hudType == 'profile'">Creator</span>
        </router-link>
      </li>
    </ul>

    <router-link
      class="link-see-all"
      :to="{ name: 'searchSingle', params: { titleYourContent } }"
      v-if="searches.length"
    >
      See all
      <i class="fas fa-arrow-right"></i>
    </router-link>

    <div class="nothing-found" v-if="!searches.length && !showResult">
      <i class="fas fa-search"></i>
      <span>Please, enter your search phrase to look for content!</span>
    </div>

    <!-- Your content nothing found part -->
    <div class="nothing-found" v-if="showResult && !searches.length">
      <i class="fas fa-search"></i>
      <span>Nothing found!</span>
    </div>

    <div class="hud-search-divider"></div>

    <!-- ALL LOC content part -->
    <span class="title">ALL LOC</span>

    <ul class="search-results">
      <li v-for="item in locs">
        <router-link v-if="item.hudType == 'profile'" :to="{path: '/profile-detail/'+item.id}">
          <h5>{{item.creativeTitle}}</h5>
          <span>Creator</span>
        </router-link>

        <router-link
          v-if="item.hudType == 'project'"
          :to="{ path: '/project-detail/project/'+item.id}"
        >
          <h5>{{item.name}}</h5>
          <span>Project</span>
        </router-link>
      </li>
    </ul>

    <router-link
      class="link-see-all"
      :to="{ name: 'searchSingle', params: { titleAllLoc } }"
      v-if="locs.length"
    >
      See all
      <i class="fas fa-arrow-right"></i>
    </router-link>

    <div class="nothing-found" v-if="!locs.length && !showResultLoc">
      <i class="fas fa-search"></i>
      <span>Please, enter your search phrase to look for content!</span>
    </div>
    <!-- ALL LOC nothing found part -->
    <div class="nothing-found" v-if="!locs.length && !showLoad && showResultLoc">
      <i class="fas fa-search"></i>
      <span>Nothing found!</span>
    </div>

    <div class="loading" v-show="showLoad">
      <span class="fa fa-spinner fa-spin"></span>
    </div>
  </div>
</template>

<script>
export default {
  name: "searchSplitted",
  middleware: "auth",
  data() {
    return {
      titleYourContent: "YOUR CONTENT",
      titleAllLoc: "ALL LOC",
      perPage: 4,
      submitted: false,
      searches: "",
      type: "creator",
      locs: [],
      selected: [],
      searchTyping: "",
      showLoad: false,
      showResult: false,
      showResultLoc: false
    };
  },

  methods: {
    search() {
      let _this = this;
      this.showLoad = true;
      axios
        .get(
          apiRoute +
            "/user/hud/search?perPage=" +
            this.perPage +
            "&type=" +
            this.selected +
            "&search=" +
            this.searchTyping,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.searches = response.data.data;
          if (response.data.data.length == 0) {
            this.showResult = true;
          }
          this.showLoad = false;
        })
        .catch(error => {
          this.showLoad = false;
        });
    },

    getLoc() {
      let _this = this;
      this.showLoad = true;
      axios
        .get(
          apiRoute +
            "/user/loc?perPage=" +
            this.perPage +
            "&search=" +
            this.searchTyping +
            "&type=" +
            this.selected,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.locs = response.data.data;
          if (response.data.data.length == 0) {
            this.showResultLoc = true;
          }
          this.showLoad = false;
        })
        .catch(error => {
          this.showLoad = false;
        });
    }
  },

  created() {
    bus.$on("filters", data => {
      if (data) {
        this.selected = data;
        if (this.searches.length || this.locs.length) {
          this.showLoad = true;
          this.search();
          this.getLoc();
        }
      }
    });

    bus.$on("search", data => {
      if (data) {
        this.searchTyping = data;
        this.search();
        this.getLoc();
      }
    });
  },

  mounted() {}
};
</script>
