<template>
  <div>
    <!--Mobile top navbar starts-->
    <div class="navbar-mobile d-block d-md-none">
      <nav class="navbar fixed-top headroom" :class="{'headroom-unpinned': scrolled}">
        <button class="navbar-button" type="button" v-on:click="openMobileLateralMenu">
          <i class="loc-icon loc-hamburger"></i>
        </button>

        <button
          class="navbar-button"
          type="button">
          <i  v-on:click="$router.push({ name: 'searchsplitted'})" class="loc-icon loc-search"></i>
        </button>
      </nav>
    </div>
    <!--Mobile top navbar ends-->

    <!-- Desktop top navbar starts -->
    <div class="navbar-desktop d-none d-md-block">
      <nav class="navbar fixed-top">
        <form class="form-inline ml-auto mr-5" onsubmit="return false">
          <div class="form-group has-search">
            <span class="fa fa-search search-icon"></span>
            <input v-if="routeName != 'searchSingle'"
              type="text" style="color: #ffffff;"
              class="form-control"
              placeholder="Search"
              v-on:click="$router.push({ name: 'searchsplitted'})"
              v-model="search" @keyup="searchResult()"
            />
            <input v-else
                   type="text"
                   class="form-control"
                   placeholder="Search"
                   style="color: white"
                   v-model="search" @keyup="searchResult()"
            />
          </div>
        </form>
        <ul class="nav">
          <li class="nav-item dropdown create">
            <button
              class="btn dropdown-toggle"
              type="button"
              id="dropdownCreateButton"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="loc-icon loc-create"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownCreateButton">

              <template v-if="subProject == 0 && user.accountType == 1"  >
                <a class="dropdown-item" data-toggle="modal" data-target="#subscriptionModal" @click="showPaymentModalFunction('project')">
                  Add New Project
                </a>
              </template>

              <template v-else>
                <router-link  class="dropdown-item" v-if="user.accountType == 1" :to="{ name: 'project.create'}">Add New Project</router-link>
              </template>

              <a  v-if="user.accountType == 1"
                class="dropdown-item coming-soon"
                href="#"
                v-tooltip.right="tooltipMsg"
              >Add New Event</a>
              <router-link class="dropdown-item"  :to="{ name: 'post.create'}">Add New Post</router-link>
              <router-link class="dropdown-item" v-if="user.accountType == 1" :to="{ name: 'poll.create'}">Create Poll</router-link>

              <template v-if="user.accountType == 1">
                <template v-if="subGroup == 0"  >
                  <a class="dropdown-item" data-toggle="modal" data-target="#subscriptionModal" @click="showPaymentModalFunction('group')">
                    Create Group
                  </a>
                </template>

                <template v-else>
                  <router-link  v-if="user.accountType == 1" :to="{ name: 'group.create'}"
                                class="dropdown-item"
                  >Create Group</router-link>
                </template>
              </template>

              <template v-else>
                <router-link  :to="{ name: 'group.create'}"
                              class="dropdown-item"
                >Create Group</router-link>
              </template>

              <router-link  v-if="user.accountType == 1" class="dropdown-item" :to="{ name: 'collection.create'}">Create Collection</router-link>
            </div>
          </li>
          <li class="nav-item" v-tooltip.bottom="tooltipMsg">
            <router-link class="nav-link disabled" to="/calendar">
              <i class="far fa-calendar"></i>
            </router-link>
          </li>
          <li class="nav-item" v-tooltip.bottom="tooltipMsg">
            <router-link class="nav-link disabled" to="/messages">
              <i class="fas fa-envelope"></i>
            </router-link>
          </li>

          <li class="nav-item dropdown notifications" v-if="notifications.length > 0">
            <button
              class="btn dropdown-toggle"
              type="button"
              id="dropdownNotifications"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="fas fa-bell"></i>
              <span class="number" v-if="notifications[0] && notifications[0].notificationCount < 100 && notifications[0].showNotificationCount">
                {{notifications[0].notificationCount}}</span>
              <i class="fas fa-circle" v-if="notifications[0] && notifications[0].notificationCount  >= 100"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-notification"   aria-labelledby="dropdownNotifications">
              <button class="btn"  @click="markAllAsReadNavBar">Mark all as read</button>
              <div class="notifications">
                <div class="notification" v-for=" (notification, key) in notifications" v-if="key < 5 && notification.profile" v-bind:class="{new: notification.isRead == 0}">

                  <router-link :to="{ path: '/project-detail/project/'+notification.project.id+'/'+notification.id}"
                               v-if="notification.project && notification.type == 'like' && notification.summary == 'project'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                    liked your project  {{notification.project.name}}
                  </router-link>

                  <router-link v-if="!notification.project && notification.type == 'like' && notification.summary == 'project'" :to="{ path: '/not-found/'+notification.id}">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                    liked your project  {{notification.name}}
                  </router-link>

                  <router-link :to="{ path: '/profile-detail/'+notification.event.profileId}" v-if="notification.type == 'like' && notification.summary == 'event'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                    liked your event  {{notification.event.name}}
                  </router-link>

                  <router-link :to="{ path: '/polls/poll-detail/'+notification.poll.id+'/'+notification.id}" v-if="notification.poll &&  notification.type == 'like' && notification.summary == 'poll'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                    liked your poll  {{notification.poll.name}}
                  </router-link>

                  <router-link :to="{ path: '/not-found/'+notification.id}"  v-if="!notification.poll &&  notification.type == 'like' && notification.summary == 'poll'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                    liked your poll  {{notification.name}}
                  </router-link>

                  <router-link :to="{ path: '/post/'+notification.post.profileId+'/post-detail/'+notification.post.id+'/'+notification.id}" v-if="notification.post && notification.type == 'like' && notification.summary == 'post'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                    liked your post
                  </router-link>

                  <router-link :to="{ path: '/not-found/'+notification.id}" v-if="!notification.post && notification.type == 'like' && notification.summary == 'post'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                    liked your post
                  </router-link>

                  <router-link :to="{path: '/profile-detail/' + notification.profile.id}" v-if="notification.profile && notification.type == 'connect' && notification.summary == 'user'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    <div>
                      <p>
                        {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                        sent  connect request   {{notification.profile.creativeTitle}} <br>
                        Do you accept? <br>
                      </p>
                      <button type="button" @click="acceptNot($event, notification.id)" class="btn btn-connect">Yes</button>
                      <button type="button" @click="declineNot($event, notification.id)" class="btn btn-connect">No</button>
                    </div>
                  </router-link>

                  <router-link :to="{ path: '/profile-detail/'+notification.profile.id+'/'+notification.id}" v-if="notification.profile && notification.type == 'follow' && notification.summary == 'user'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                    began following your profile  {{notification.profile.creativeTitle}}
                  </router-link>
                  <router-link :to="{ path: '/profile-detail/'+notification.profile.id+'/'+notification.id}" v-if="notification.profile && notification.type == 'message' && notification.summary == 'message'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    The new message was received from {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                  </router-link>
                  <router-link :to="{ path: '/profile-detail/'+notification.profile.id+'/'+notification.id}" v-if="notification.profile && notification.type == 'accept' && notification.summary == 'user'">
                    <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                      has accepted your connect request
                  </router-link>
                </div>
                <div class="loading loading-notification" v-show="showLoaderNotification">
                  <span class="fa fa-spinner fa-spin"></span>
                </div>
              </div>
              <router-link  class="link mt-2" to="/notifications">See all</router-link>
            </div>
          </li>
          <li class="nav-item dropdown">
            <button
              class="btn dropdown-toggle"
              type="button"
              id="dropdownMenuButton"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <img v-if="userAvatar" :src="userAvatar"  alt="default" class="user-img" >
              <img v-else src="/images/user8-128x128.png"  alt="default" class="user-img" >
              {{user.firstName}} {{user.lastName}}
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
              <router-link class="dropdown-item" to="/profiles">
                <i class="fas fa-user"></i> My Profiles
              </router-link>
              <a style="cursor: pointer" class="dropdown-item" @click="logout">
                <i class="fas fa-sign-out-alt"></i>
                Log out
              </a>
            </div>
          </li>
        </ul>
      </nav>
    </div>

    <ModalPayment v-if="(subProject == 0 && showPaymentModal == true) || (subGroup == 0 && showPaymentModal == true)">
      <div slot="body">{{messages}}</div>
    </ModalPayment>
  </div>
</template>

<script>
  import {helpers} from '../mixins/helpers';
  import Cookies from 'js-cookie';
  import ModalPayment from "./modals/ModalPayment";
  export default {
    name: "Header",
    components: {ModalPayment},
    mixins: [helpers],
    data() {
      return {
        tooltipMsg: "Coming Soon",
        limitPosition: 400,
        scrolled: false,
        lastPosition: 0,
        appName: window.config.appName,
        isActive: true,
        isShow: false,
        token: Cookies.get('token'),
        user: {},
        userAvatar: '',
        avatar: '',
        isOpen: false,
        notifications: {},
        search: '',
        totalNewNotifications: 2,
        totalNotifications: 100,
        showLoaderNotification:false,
        subProject: 0,
        showPaymentModal: false,
        subGroup: 0,
        type: '',
        subscription: ''

      };
    },

    computed: {
      routeName () {
        return this.$route.name
      }
    },

    methods: {
      //Open Mobile Lateral Menu function
      openMobileLateralMenu() {
        this.$root.$emit("open mobile menu", true);
      },

      searchResult() {
        bus.$emit('search', this.search)
      },

      //Hangle scroll to hide menu
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

      getUser() {
        let _this = this;
        axios.get(apiRoute + '/user',  this.token).then(response => {
          _this.user = response.data.data;
          if (response.data.data.avatarUri) {
            _this.userAvatar  = '/storage/avatarImage/' + response.data.data.id + '/' + response.data.data.avatarUri;
          }
        }).catch(error => {

        });
      },


      getNotifications(n) {
        this.showLoaderNotification = true
        if(n != 're'){
          // bus.$emit('notificationRefresh', 1)
        }else {
          n = 0
        }

        let _this = this;
        axios.get(apiRoute + '/user/notifications?page='+n,  this.token).then(response => {
          _this.notifications = response.data.data;
          this.showLoaderNotification = false
        }).catch(error => {
          this.showLoaderNotification = false
        });
      },


      getNotification(notifcationId, userId) {
        let _this = this;
        if(userId == this.user.id){
          axios.get(apiRoute + '/user/notifications/'+notifcationId,  this.token).then(response => {
            _this.notifications.unshift(response.data.data);
          }).catch(error => {

          });
        }
      },


      markAllAsReadNavBar() {
        let _this = this;

        axios.post(apiRoute+ '/notifications/read').then(response => {
          _this.getNotifications()
        }).catch(function (error) {

        });

      },


      logout() {
        let _this = this;
        axios.post(apiRoute + '/user/logout',  this.token).then(response => {
          Cookies.remove('token')
          _this.$router.push({path: '/', name: 'welcome'})
        }).catch(error => {

        });
      },


      dateChange: function (date) {
        return moment(date).format('lll');
      },

      openNotification() {
        this.isOpen = !this.isOpen;
        if(this.isOpen){
          this.getNotifications(1);
        }
      },

      acceptNot(e, notificationId){
        e.stopPropagation();
        let _this = this;
        axios.post(apiRoute + '/user/connection/'+notificationId+'/accept',  this.token).then(response => {
          $(".dropdown-menu-notification").removeClass('show');
          _this.getNotifications(0);
        }).catch(error => {

        });

      },

      declineNot(e, notificationId){
        e.stopPropagation();
        let _this = this;
        axios.delete(apiRoute + '/user/notifications/'+ notificationId,  this.token).then(response => {
          $(".dropdown-menu-notification").removeClass('show');
          _this.getNotifications(0);
        }).catch(error => {

        });
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


      toggle() {
        this.isActive = !this.isActive;
        if (this.isActive) {
          this.isShow = false;
        } else {
          this.isShow = true;
        }

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
      bus.$on('userData', (data) => {
        this.user.firstName = data.firstName;
        this.user.lastName  = data.lastName;
        if(data.avatarUri) {
          this.userAvatar  = '/storage/avatarImage/' + data.id + '/' + data.avatarUri;
        }
      })

      bus.$on('notRefresh', (data) => {
        if(data){
          this.getNotifications('re')
        }
      })

      bus.$on('closePaymentModal', (data) => {
        if(data){
          this.showPaymentModal = false;
        }
      })

      bus.$on('refreshSubscription', (data) => {
        if(data){
          this.getUser();
          this.getUserProjectSubscription();
          this.getUserGroupSubscription();
          this.$forceUpdate();
        }
      })
    },

    destroyed() {
      window.removeEventListener("scroll", this.handleScroll);
    },

    mounted() {
      $(document).ready(function() {
        $(document).on("click",".dropdown-menu-notification",function(e) {
          e.stopPropagation();
        });
      });


      this.getUserProjectSubscription();
      this.getUserGroupSubscription();
      this.getUser();
      this.getNotifications(0);
      window.Echo.channel('notification').listen('NewNotification', (e) =>{
        this.getNotification(e.notificationId, e.userId);
      })
    }
  };
</script>

<style scoped lang="scss">
  /* Mobile navbar style */

  .btn-connect {
    border: 1px solid #fff;
    border-radius: 20px;
    transition: 0.3s;
    color: #fff;
    &:hover {
      color: #9d72ff;
      border: 1px solid #9d72ff;
      transition: 0.3s;
    }

    display: inline-block !important;
    padding: 3px 15px 3px 15px !important;
  }

  .navbar-mobile {
    .navbar {
      padding: 10px;
      background-color: #333;
      .navbar-button {
        border: none;
        color: #fff;
        padding: 0;
        background-color: transparent;
        font-size: 1.3rem;
      }
      /* Styles to handle scroll nav hide */
      &.headroom {
        will-change: transform;
        transition: transform 200ms linear;
      }
      &.headroom-pinned {
        transform: translateY(0%);
      }
      &.headroom-unpinned {
        transform: translateY(-100%);
      }
    }

  }

  /* Desktop navbar style */
  .navbar-desktop {
    .navbar {
      background-color: #242424;
    }

    .nav-link {
      color: #fff;
      font-size: 1rem;
      &:hover {
        color: #c2c2c2;
      }
      &.disabled {
        color: #62666a;
      }
    }
    .has-search {
      width: 200px;
      input {
        border-radius: 50px;
        background-color: #62666a;
        border: none;
        padding-left: 2.3rem;
        width: 100%;
        height: 1.8rem;
        &::placeholder {
          color: #fff;
        }
      }

      .search-icon {
        position: absolute;
        z-index: 2;
        display: block;
        width: 2.3rem;
        line-height: 2rem;
        text-align: center;
        pointer-events: none;
        color: #fff;
      }
    }
    .dropdown {
      button {
        padding: 0;
        color: #fff;
        &:after {
          display: none;
        }
        .user-img {
          border-radius: 50%;
          width: 40px;
        }
      }
      .dropdown-menu {
        margin-top: 10px;
        background-color: #242424;
        .dropdown-item {
          color: #fff;
          font-size: 1rem;
          &:hover,
          &:active,
          &:focus {
            background-color: #4a4a4a;
            color: #c2c2c2;
          }
          &.coming-soon {
            cursor: default;
            color: #505050;
            &:hover,
            &:active,
            &:focus {
              background-color: transparent;
              color: #505050;
            }
          }
        }
      }
      &.create {
        padding: 0.5rem 1rem;
        i {
          font-size: 1.2rem;
        }
        .dropdown-menu {
          text-align: center;
        }
      }
      &.notifications {
        position: relative;
        padding: 0.5rem 1rem;
        .dropdown-toggle {
          i {
            font-size: 1.1rem;
          }
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
        .dropdown-menu {
          width: 350px;
          padding: 10px;
          .notifications {
            margin-top: 10px;
            .notification {
              border-top: 1px solid #787878;
              padding: 10px 5px;
              &.new {
                background: #646464;
              }
              a {
                font-size: 0.9rem;
                text-decoration: none;
                display: flex;
                justify-content: flex-start;
                color: #fff;
                transition: 0.3s;
                img {
                  margin-right: 10px;
                  width: 40px;
                  height: 40px;
                  border-radius: 50%;
                }
                &:hover {
                  color: #9d72ff;
                  transition: 0.3s;
                }
              }
              &:last-child {
                border-bottom: 1px solid #787878;
              }
            }
          }
          .btn,
          .link {
            text-decoration: none;
            text-align: center;
            display: block;
            max-width: 150px;
            margin-left: auto;
            margin-right: auto;
            transition: 0.3s;
            color: #fff;
            &:hover {
              color: #9d72ff;
              transition: 0.3s;
            }
          }
        }
      }
    }
  }

  .notification a {
    display: flex;
    align-items: center;
  }

  .loading-notification{
    position: absolute;
  }


</style>
