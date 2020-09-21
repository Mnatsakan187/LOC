<template>
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3 mt-3">
          <div class="title my-3">Your notifications</div>
          <button class="btn" @click="markAllAsRead">Mark all as read</button>
          <div class="notifications" v-if="!noNotification">
            <div v-for="notification in notifications" class="notification" v-bind:class="{new: notification.isRead == 0}">

              <router-link :to="{ path: '/project-detail/project/'+notification.project.id+'/'+notification.id}"
                           v-if="notification.project && notification.type == 'like' && notification.summary == 'project'">
                <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                <img v-else src="/images/user8-128x128.png"/>
                 {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                   liked your project  {{notification.project.name}}
              </router-link>

              <router-link :to="{ path: '/not-found/'+notification.id}"
                           v-if="!notification.project && notification.type == 'like' && notification.summary == 'project'">
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

              <router-link :to="{ path: '/polls/poll-detail/'+notification.poll.id+'/'+notification.id}" v-if="notification.poll && notification.type == 'like' && notification.summary == 'poll'">
                <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                <img v-else src="/images/user8-128x128.png"/>
                  {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                  liked your poll  {{notification.poll.name}}
              </router-link>

              <router-link :to="{ path: '/not-found/'+notification.id}" v-if="!notification.poll && notification.type == 'like' && notification.summary == 'poll'">
                <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                <img v-else src="/images/user8-128x128.png"/>
                {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                liked your poll  {{notification.name}}
              </router-link>

              <router-link  :to="{ path: '/post/'+notification.post.profileId+'/post-detail/'+notification.post.id+'/'+notification.id}" v-if="notification.post && notification.type == 'like' && notification.summary == 'post'">
                <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                <img v-else src="/images/user8-128x128.png"/>

                  {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                  liked your post
              </router-link>

              <router-link  :to="{ path: '/not-found/'+notification.id}"   v-if="!notification.post && notification.type == 'like' && notification.summary == 'post'">
                <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                <img v-else src="/images/user8-128x128.png"/>

                {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                liked your post
              </router-link>

              <a href="javascript:void(0)" v-if="notification.profile && notification.type == 'connect' && notification.summary == 'user'">
                <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                <img v-else src="/images/user8-128x128.png"/>
                  <div>
                    <p>
                      {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                      sent  connect request   {{notification.profile.creativeTitle}} <br>
                      Do you accept? <br>
                    </p>
                    <button  @click="acceptNot($event, notification.id)" class="btn ">Yes</button>
                    <button  @click="declineNot($event, notification.id)" class="btn">No</button>
                  </div>
              </a>
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
              <router-link  :to="{ path: '/profile-detail/'+notification.profile.id+'/'+notification.id}" v-if="notification.profile && notification.type == 'accept' && notification.summary == 'user'">
                <img v-if="notification.notificationUser.avatarUri"  :src="'/storage/avatarImage/' + notification.notificationUser.id + '/' + notification.notificationUser.avatarUri"/>
                <img v-else src="/images/user8-128x128.png"/>
                  {{notification.notificationUser.firstName}} {{notification.notificationUser.lastName}}
                     has accepted your connect request
              </router-link>
            </div>
          </div>
          <div v-if="noNotification" class="no-notifications mt-5">You do not have any notifications yet!</div>
        </div>
      </div>
      <div v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-distance="10"></div>
      <div class="loading" v-show="showLoader">
        <span class="fa fa-spinner fa-spin"></span>
      </div>
    </div>
</template>

<script>
  import {helpers} from '../../mixins/helpers';
  export default {
    name: 'notifications',
    mixins: [helpers],
    data: () => ({
      user: {},
      notifications: {},
      pagination: {},
      noNotification: false,
      showLoader: false,
      perPage:13,
    }),


    methods: {
      getUser() {
        let _this = this;
        axios.get(apiRoute + '/user',  this.$store.getters['auth/token']).then(response => {
          _this.user = response.data.data;
          if (response.data.data.avatarUri) {
            _this.userAvatar  = '/storage/avatarImage/' + response.data.data.id + '/' + response.data.data.avatarUri;
          }
        }).catch(error => {

        });
      },

      getNotifications(data) {
        bus.$emit('notRefresh', 1)
        if(data != 1){
          this.showLoader = true
        }

        let _this = this;
        axios.get(apiRoute + '/user/notifications?page=1' + '&perPage='+this.perPage, this.$store.getters['auth/token']).then(response => {
          if(response.data.data.length == 0){
            _this.noNotification = true
          }
          _this.notifications = response.data.data;
          _this.pagination = response.data.meta;
          this.showLoader = false;
        }).catch(error => {
          this.showLoader = false;
        });
      },


      loadMore: function() {
          this.busy = true;
          this.showLoader = true;
          this.perPage++;
          this.getNotifications()
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

      openModalRe(id){
        $('#remove-notification-' + id).modal('show');
      },

      dateChange: function (date) {
        return moment(date).format('lll');
      },

      deleteNotification(notificationId){
        let _this = this;
        axios.delete(apiRoute+ '/user/notifications/'+notificationId).then(response => {
          $('#remove-notification-' + notificationId).modal('hide');
          _this.getNotifications()
        }).catch(function (error) {

        });
      },


    },

    created() {
      bus.$on('notificationRefresh', (data) => {
        if(data){
          this.getNotifications(data)
        }
      })
    },

    mounted() {
      $(".dropdown-menu-notification").removeClass('show');
      this.getNotifications();
      let _this = this;
      window.Echo.channel('notification').listen('NewNotification', (e) =>{
        _this.getNotifications()
      })

    }
  }
</script>

<style scoped lang="scss">
  .title {
    font-size: 1.5rem;
    font-family: EncodeSansSemiBold;
  }
  .btn {
    border: 1px solid #fff;
    border-radius: 20px;
    transition: 0.3s;
    color: #fff;
    &:hover {
      color: #9d72ff;
      border: 1px solid #9d72ff;
      transition: 0.3s;
    }
    padding: 3px 15px 3px 15px !important;
  }
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

  .no-notifications {
    text-align: center;
    font-size: 1.2rem;
    color: #fff;
  }

  .notification a {
    display: flex;
    align-items: center;
  }

</style>
