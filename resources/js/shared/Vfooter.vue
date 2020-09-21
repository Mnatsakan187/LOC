<template>
  <!-- Mobile footer -->
  <div class="d-block d-md-none">
    <!-- Overlay -->
    <div id="overlay" :class="{ show: showFooterOverlay }"></div>
    <nav
      class="footer-nav navbar navbar-expand-lg fixed-bottom footerroom"
      :class="{'footerroom-unpinned': scrolled}"
    >
      <div class="collapse navbar-collapse" id="navbarCreateList">
        <div class="list-create navbar-nav">

          <template v-if="subProject == 0 && user.accountType == 1">
            <a data-toggle="collapse"
               data-target="#navbarCreateList" @click="showPaymentModalFunction('project')"
                         class="nav-item nav-link"
                         v-on:click="toggleOverlay"
            >Add New Project</a>
          </template>

          <template v-else>
            <router-link v-if="user.accountType == 1" :to="{ name: 'project.create'}"
                         class="nav-item nav-link"
                         data-toggle="collapse"
                         data-target="#navbarCreateList"
                         v-on:click="toggleOverlay"
            >Add New Project</router-link>
          </template>

          <a v-if="user.accountType == 1" class="nav-item nav-link coming-soon" href="#">
            Add New Event
            <span class="coming-soon">Coming Soon</span>
          </a>
          <router-link   :to="{ name: 'post.create'}"
            class="nav-item nav-link"
            href="#"
            data-toggle="collapse"
            data-target="#navbarCreateList"
          >Add New Post</router-link>
          <router-link v-if="user.accountType == 1" :to="{ name: 'poll.create'}"
            class="nav-item nav-link"
            data-toggle="collapse"
            data-target="#navbarCreateList"
            v-on:click="toggleOverlay"
          >Create Poll</router-link>

          <template v-if="subGroup == 0">
            <a
                          class="nav-item nav-link"
                          data-toggle="collapse"
                          data-target="#navbarCreateList"
                          v-on:click="toggleOverlay"
                          @click="showPaymentModalFunction('group')"
            >
              Create Group
            </a>
          </template>

          <template v-else>
            <router-link
                          class="nav-item nav-link"
                          data-toggle="collapse"
                          data-target="#navbarCreateList"
                          v-on:click="toggleOverlay"
                          :to="{ name: 'group.create'}">
              Create Group
            </router-link>
          </template>
          <router-link v-if="user.accountType == 1"  :to="{ name: 'collection.create'}"
            class="nav-item nav-link"
            data-toggle="collapse"
            data-target="#navbarCreateList"
            v-on:click="toggleOverlay"
          >Create Collection</router-link>
        </div>
      </div>
      <div class="navbar-links">
        <router-link class="nav-link" :to="{ name: 'my.feed' }">
          <i class="loc-icon loc-home"></i>
        </router-link>
        <a
          class="nav-link coming-soon"
          href="#"
          v-tooltip.top="{
          content:'Coming Soon',
          trigger:'click'
        }"
        >

          <i class="loc-icon loc-event"></i>
        </a>
        <a
          class="nav-link create-button-desktop mr-2"
          href="#"
          data-toggle="collapse"
          data-target="#navbarCreateList"
          aria-controls="navbarNavAltMarkup"
          aria-expanded="false"
          aria-label="Toggle navigation"
          v-on:click="toggleOverlay"
        >
          <i class="loc-icon loc-create"></i>
        </a>


        <router-link class="nav-link notifications" to="/notifications">
          <i class="loc-icon loc-notification"></i>
          <span class="number" v-if="notifications[0] && notifications[0].notificationCount < 100 && notifications[0].showNotificationCount">
                {{notifications[0].notificationCount}}</span>
          <i class="fas fa-circle" v-if="notifications[0] && notifications[0].notificationCount  >= 100"></i>
        </router-link>


        <router-link class="nav-link" to="/profiles">
          <i class="loc-icon loc-profile"></i>
        </router-link>
      </div>
    </nav>
    <ModalPayment v-if="(subProject == 0 && showPaymentModal == true) || (subGroup == 0 && showPaymentModal == true)">
      <div slot="body">{{messages}}</div>
    </ModalPayment>
  </div>
  <!-- Mobile footer ends-->
</template>

<script>
  import ModalPayment from "./modals/ModalPayment";
  export default {
    name: "Footer",
    components: {ModalPayment},
    data() {
      return {
        showFooterOverlay: false,
        limitPosition: 400,
        scrolled: false,
        lastPosition: 0,
        user: {},
        notifications: {},
        subProject: 0,
        showPaymentModal: false,
        subGroup: 0,
        subscription: ''
      };
    },

    methods: {
      getUser() {
        let _this = this;
        axios.get(apiRoute + '/user',  this.token).then(response => {
          _this.user = response.data.data;
        }).catch(error => {

        });
      },

      toggleOverlay() {
        this.showFooterOverlay = !this.showFooterOverlay;
      },


      getUserProjectSubscription(){
        let _this = this;
        axios.get(apiRoute + '/projects/subscription', this.$store.getters['auth/token']).then(response => {
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
      },


      getUserGroupSubscription(){
        let _this = this;
        axios.get(apiRoute + '/groups/subscription', this.$store.getters['auth/token']).then(response => {
          if(!response.data.subscription) {
            _this.subGroup = 0
            _this.messages = "To create a new profile, would you like to upgrade you plan?"
          }else{
            if(!response.data.newProfile && response.data.subscription){
              _this.messages = "You have reached limit for profile creation. Would you like to upgrade you plan?"
              _this.subGroup = 0
            }else{
              _this.subGroup = 1
            }
          }

        }).catch(error => {

        })
      },


      handleScroll() {
        if (
          this.lastPosition < window.scrollY &&
          this.limitPosition < window.scrollY
        ) {
          this.scrolled = true;
        }

        if (this.lastPosition > window.scrollY) {
          this.scrolled = false;
        }

        this.lastPosition = window.scrollY;
        //only shows footer back at less than 250px from top
        //this.scrolled = window.scrollY > 250;
      },

      getNotifications(n) {
        let _this = this;
        axios.get(apiRoute + '/user/notifications?page='+n,  this.token).then(response => {
          _this.notifications = response.data.data;
        }).catch(error => {

        });
      },
    },
    created() {
      window.addEventListener("scroll", this.handleScroll);
      bus.$on('notRefresh', (data) => {
        if(data){
          this.getNotifications('re')
        }
      })

      bus.$on('overlay', (data) => {
        if(data){
          this.showFooterOverlay = false
        }
      }),

      bus.$on('closePaymentModal', (data) => {
        if(data){
          this.showPaymentModal = false;
        }
      })

      bus.$on('refreshSubscription', (data) => {
        if(data){
          this.getUserProjectSubscription();
          this.getUserGroupSubscription();
        }
      })
    },

    mounted(){
      this.getUser();
      this.getNotifications(0);
      this.getUserGroupSubscription();
      this.getUserProjectSubscription();
    },

    destroyed() {
      window.removeEventListener("scroll", this.handleScroll);
    }
  };
</script>

<style scoped lang="scss">
  /* Overlay starts */
  #overlay {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1031;
    &.show {
      display: block;
    }
  }
  /* Overlay ends */
  /* Footer nav for mobile devices */
  .footer-nav {
    z-index: 1032;
    background-color: #333;
    padding: 0;
    i {
      color: #fff;
      font-size: 1.5rem;
    }
    .navbar-collapse {
      background-color: #000;
      text-align: center;
      .nav-link {
        color: #fff;
        font-family: EncodeSansRegular;
        font-size: 1.3rem;
        padding: 15px 0;
        border-bottom: 2px solid #333;
        &.grey {
          color: #989898;
        }
        &.coming-soon {
          color: #505050;
          cursor: default;
          span.coming-soon {
            color: #fff;
            font-size: 0.8rem;
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 5px;
            margin-left: 10px;
          }
        }
      }
    }
    .navbar-links {
      padding: 10px 0;
      display: flex;
      justify-content: space-between;
      width: 90%;
      margin: 0 auto;
      .coming-soon {
        cursor: default;
        i {
          color: #505050;
        }
      }
      .notifications {
        position: relative;
        .number {
          border-radius: 50%;
          width: 20px;
          height: 20px;
          font-weight: bold;
          font-size: 0.8rem;
          display: flex;
          justify-content: center;
          align-items: center;
          color: #fff;
          background: red;
          position: absolute;
          top: 0;
          right: 0;
        }
        .fa-circle {
          color: red;
          position: absolute;
          top: 5px;
          right: 10px;
          font-size: 0.8rem;
        }
      }
    }
    /* Styles to show/hide footer on scroll */
    &.footerroom {
      will-change: transform;
      transition: transform 200ms linear;
    }
    &.footerroom-pinned {
      transform: translateY(0%);
    }
    &.footerroom-unpinned {
      transform: translateY(+100%);
    }
  }
</style>
