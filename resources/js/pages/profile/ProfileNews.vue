<template>
  <div class="row profile-news">
    <div class="col">
      <NewsFeed v-bind:newsList="newsList"></NewsFeed>
      <ModalConfirmation v-if="showConfModal" @close="showConfModal = false">
        <div slot="body">Are you sure, you want to delete this {{typeofContent}}?</div>
      </ModalConfirmation>

      <div class="profile-empty-content mt-5" v-if="empty">
        <div class="line bolder">This profile does not have any content yet.</div>
      </div>
      <div class="profile-empty-content mt-5" v-if="emptyData == 1">
        <div class="line bolder">Your feed is empty.</div>
        <div class="line" v-if="user.accountType == 1">Create your first post or project!</div>
        <div class="line" v-else>Create your first post</div>
        <template v-if="subProject == 0 && user.accountType == 1">
          <button class="btn" data-toggle="modal" data-target="#subscriptionModal" @click="showPaymentModalFunction('project')">
            Add first Project
          </button>
        </template>
        <template v-else>
          <button
            class="btn"
            v-on:click="$router.push('/projects/create')"
            v-if="user.accountType == 1"
          >Add first project</button>
        </template>

        <button class="btn" v-on:click="$router.push('/posts/create')">Add post</button>
      </div>

      <ModalPayment v-if="subProject == 0 && showPaymentModal == true">
        <div slot="body">{{messages}}</div>
      </ModalPayment>
    </div>

    <div class="loading" v-show="showLoader">
      <span class="fa fa-spinner fa-spin"></span>
    </div>
  </div>
</template>

<script>
import ModalConfirmation from "../../shared/modals/ModalConfirmation";
import NewsFeed from "../../shared/newsfeed/NewsFeed.vue";
import ModalPayment from "../../shared/modals/ModalPayment";
export default {
  name: "ProfileNews",
  middleware: "auth",
  components: {
    NewsFeed,
    ModalConfirmation,
    ModalPayment
  },
  data: function() {
    return {
      newsList: [],
      typeofContent: "",
      showConfModal: false,
      showLoader: false,
      emptyData: 0,
      user: {},
      profile: {},
      empty: 0,
      showPaymentModal: false,
      subProject: 0,
      type: '',
      messages: ''
    };
  },

  methods: {
    profileView() {
      let _this = this;
      this.showLoader = true;
      axios
        .get(
          apiRoute + "/user/profiles/" + this.profileId + "/view",
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.newsList = response.data;
          if (!response.data.length) {
            if (this.user.id == this.profile.userId) {
              this.emptyData = 1;
            } else {
              this.empty = 1;
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
      let _this = this;
      axios
        .get(
          apiRoute + "/user/profiles/" + this.profileId,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.profile = response.data.data;
          _this.profileView();
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

    this.$root.$on("confirmation modal", openModal => {
      this.showConfModal = openModal.open;
      this.typeofContent = openModal.contentType;
    });

    bus.$on("refresh", data => {
      if (data == 1) {
        this.profileView();
      }
    });

    bus.$on("closeConfDialog", data => {
      if (data == 1) {
        this.showConfModal = false;
      }
    });

    bus.$on("like", data => {
      if (data == 1) {
        this.profileView();
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
