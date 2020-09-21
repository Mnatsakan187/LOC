 <template>
  <div class="search-single">
    <span class="title">{{title}}</span>
    <ul class="search-results">
      <li v-for="search in searches">
        <router-link v-if="search.hudType == 'project'" :to="{ path: '/project-detail/project/'+search.id}">
          <span v-if="search.hudType == 'project'">{{search.name}}</span>
          <span v-if="search.hudType == 'project'">Project</span>
        </router-link>
        <router-link v-else :to="{path: '/profile-detail/'+search.id}" >
          <span v-if="search.hudType == 'profile'">{{search.creativeTitle}}</span>
          <span v-if="search.hudType == 'profile'">Creator</span>
        </router-link>
      </li>
    </ul>
    <!-- Nothing found part -->
    <div class="nothing-found" v-if="!searches.length && !showLoad">
      <i class="fas fa-search"></i>
      <span>Nothing found</span>
    </div>

    <div class="loading" v-show="showLoad">
      <span class="fa fa-spinner fa-spin"></span>
    </div>
  </div>
</template>

<script>
export default {
  name: "searchSingle",
  middleware: 'auth',
  props: ["titleAllLoc", "titleYourContent"],
  data() {
    return {
      title: "Content",
      perPage:100,
      submitted: false,
      searches: [],
      selected: [],
      searchTyping: '',
      showLoad:false,

    };
  },


  methods: {
    search() {
      let _this = this;
      this.showLoad = true
      axios.get(apiRoute + '/user/hud/search?perPage='+this.perPage+'&type='+this.selected+'&search='+this.searchTyping,
        this.$store.getters['auth/token']).then(response => {
        _this.searches = response.data.data;
        this.showLoad = false
      }).catch(error => {
        this.showLoad = false
      })
    },

    getLoc() {
      let _this = this;
      this.showLoad = true
      axios.get(apiRoute + '/user/loc?perPage='+this.perPage+'&search='+this.searchTyping+'&type='+this.selected,   this.$store.getters['auth/token']).then(response => {
        _this.searches = response.data.data;
        this.showLoad = false
      }).catch(error => {
        this.showLoad = false;
      })
    },

  },

  created() {
    bus.$on('filters', (data) => {
      if(data){
        this.searchTyping = '',
        this.selected = data;
          if(this.titleAllLoc == 'ALL LOC' ){
            this.getLoc();
          }else{
            this.search();
          }
      }
    })

    bus.$on('search', (data) => {

      if(data){
        this.searchTyping = data;

        if(this.titleAllLoc == 'ALL LOC' ){
          this.getLoc();
        }else{
          this.search();
        }

      }
    })
  },

  mounted() {
    if (this.titleAllLoc) {
      this.title = this.titleAllLoc;
    }

    if(this.titleAllLoc == 'ALL LOC' ){
      this.getLoc();
    }else{
      this.search();
    }

    if (this.titleYourContent) {
      this.title = this.titleYourContent;

    }
  }
};
</script>
