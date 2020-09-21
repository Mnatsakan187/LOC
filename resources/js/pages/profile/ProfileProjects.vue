<template>
  <div class="row profile-projects">
    <div class="col">
      <TilesList v-bind:tilesList="tilesList"></TilesList>
      <div class="profile-empty-content mt-5" v-if="emptyProjects == 1">
        <div class="line bolder">This profile does not have any projects yet.</div>
      </div>
      <div class="profile-empty-content mt-5" v-if="emptyProjectsData == 1">
        <div class="line bolder">You do not have any projects yet.</div>
        <div class="line">Create your first project!</div>
        <template v-if="subProject == 0 && user.accountType == 1">
          <button class="btn" data-toggle="modal" data-target="#subscriptionModal" @click="showPaymentModalFunction('project')">
            Add first Project
          </button>
        </template>
        <template v-else>
          <button class="btn" v-on:click="$router.push('/projects/create')">Add first project</button>
        </template>
      </div>

      <div class="loading" v-show="showLoader">
        <span class="fa fa-spinner fa-spin"></span>
      </div>
      <ModalPayment v-if="subProject == 0 && showPaymentModal == true">
        <div slot="body">{{messages}}</div>
      </ModalPayment>
    </div>
  </div>
</template>
<script>
import TilesList from "../../shared/tiles/TilesList.vue";
import ModalPayment from "../../shared/modals/ModalPayment";
export default {
  name: "ProfileProjects",
  middleware: "auth",
  components: {
    TilesList, ModalPayment
  },
  data: function() {
    return {
      tilesList: [],
      profileId: "",
      showLoader: false,
      emptyProjectsData: 0,
      user: {},
      profile: {},
      emptyProjects: 0,
      showPaymentModal: false,
      subProject: 0,
      type: '',
      messages: ''
    };
  },

  methods: {
    getProjects() {
      let _this = this;
      this.showLoader = true;
      axios
        .get(
          apiRoute + "/user/profiles/" + this.profileId + "/projects",
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.tilesList = response.data.data;

          if (!response.data.data.length) {
            if (this.user.id == this.profile.userId) {
              this.emptyProjectsData = 1;
            } else {
              this.emptyProjects = 1;
            }
          }

          this.showLoader = false;
        })
        .catch(error => {
          this.showLoader = false;
        });
    },
    getUser() {
      this.showLoader = true;
      axios
        .get(apiRoute + "/user", this.$store.getters["auth/token"])
        .then(response => {
          this.user = response.data.data;
          this.getProfile();
        })
        .catch(error => {});
    },

    getProfile() {
      this.showLoader = true;
      axios
        .get(
          apiRoute + "/user/profiles/" + this.profileId,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          this.profile = response.data.data;
          this.getProjects();
        })
        .catch(error => {});
    },

    getUserProjectSubscription(){
      let _this = this;
      axios.get(apiRoute + '/projects/subscription', this.$store.getters['auth/token']).then(response => {
        this.subscription = response.data.subscription;
        if(!response.data.subscription) {
          _this.subProject = 0
          _this.messages = "To create a new profile, would you like to upgrade you plan?"
        }else{
          if(!response.data.newProfile && response.data.subscription){
            _this.messages = "You have reached limit for profile creation. Would you like to upgrade you plan?"
            _this.subProject = 0
          }else{
            _this.subProject = 1
          }
        }
      }).catch(error => {

      })
    },

    showPaymentModalFunction(type) {
      this.showPaymentModal = true;
      this.type = type
      if(!this.subscription){
        this.messages = "To create a new "+type+", would you like to upgrade you plan?"
      }else{
        this.messages = "You have reached limit for "+type+" creation. Would you like to upgrade you plan?"
      }
    }
  },

  created() {
    this.profileId = this.$route.params.profileId;

    bus.$on("refresh", data => {
      if (data == 1) {
        this.getUser();
      }
    });

    bus.$on('closePaymentModal', (data) => {
      if(data){
        this.showPaymentModal = false;
      }
    })
  },

  mounted() {
    this.getUser();
    this.getUserProjectSubscription();
  }
};
</script>
<style lang="scss">
.profile-projects {
  .tiles {
    .tile {
      width: 220px;
      margin-bottom: 15px;
      @media (max-width: 2000px) {
        width: 30%;
        margin-left: 2.5%;
      }
      @media (max-width: 1200px) {
        width: 45%;
        margin-left: 5%;
      }
    }
  }
}
</style>
