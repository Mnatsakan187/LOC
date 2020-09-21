<template>
    <div class="profile-team-mobile">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="info-header mt-3">
              <router-link class="back-button float-left" :to="{ path: '/profile-detail/'+profileId}">
                <i class="fas fa-arrow-left"></i>
              </router-link>
              <div class="title">TEAM</div>
              <router-link class="link-edit-team" v-if="user.id == profile.userId && allTeams.length > 0" :to="{ path: '/profile/team/'+profileId}">Edit team</router-link>
            </div>
            <div class="name-image mt-5">
              <img v-if="profile.avatarUri" :src="urlProfileImage"/>
              <img v-else src="/images/user8-128x128.png"/>
              <div class="name">{{profile.creativeTitle}}</div>
              <!--<div class="creative-title">Visual artist</div>-->
            </div>
            <div class="team-text mt-5" style="text-align: center">
              [They Earn a Mention]
              <br />Check out some of the other Creators who help me do what I do.
            </div>
            <div class="team-members">
              <div class="team-member" v-for="team in allTeams">
                <img v-if="team.avatarUri" :src="'/storage/avatarImage/' + team.id + '/' + team.avatarUri"  alt="default">
                <img v-else src="/images/user8-128x128.png"  alt="default">
                <router-link :to="{ path: '/profile-detail/'+team.id}" target="_blank">
                  <span class="name">{{team.creativeTitle}}</span>
                  <span class="creative-title" v-if="team.contentPreferenceWritten == 1">Written</span>
                  <span class="creative-title" v-if="team.contentPreferenceAudio  == 1">Audio</span>
                  <span class="creative-title" v-if="team.contentPreferenceVisual == 1">Visual</span>
                  <span class="creative-title" v-if="team.contentPreferenceEvents == 1">Events</span>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<script>
  import {helpers} from '../../mixins/helpers';
  export default {
    name: "ProfileTeamMobile",
    middleware: 'auth',
    mixins: [helpers],
    data: function() {
      return {
        showModal: false,
        profile:{},
        urlProfileImage: '',
        urlBackgroundImage: '',
        backgroundShow: true,
        socialMediaLinks: {},
        posts:{},
        user: '',
        events: {},
        checkFollow: false,
        followsCount: 0,
        allTeams: [],
        checkConnected: 3
      };
    },

    methods: {
      getProfile() {
        let _this = this;
        axios.get(apiRoute + '/user/profiles/' + this.profileId, this.$store.getters['auth/token']).then(response => {
          _this.profile = response.data.data;
          _this.checkUserFollow(response.data.data.follows)
          _this.checkConnect(response.data.data.connection)
          _this.socialMediaLinks = response.data.data.socialMediaLinks;
          _this.urlProfileImage = '/storage/profiles/profilePictureImage/' + response.data.data.id + '/' + response.data.data.avatarUri;
          _this.urlBackgroundImage = '/storage/profiles/profileBackgroundImage/' + response.data.data.id + '/' + response.data.data.backgroundUri;

        }).catch(error => {

        });
      },

      checkUserFollow(follow){
        let _this = this;
        if(follow != undefined) {
          _this.followsCount = follow.length
          follow.forEach((value, key) => {
            if(value.id == _this.user.id){
              _this.checkFollow = true;
            }
          });
        }

      },

      checkConnect(connected){
        let _this = this;
        if(connected != undefined) {
          connected.forEach((value, key) => {
            if(value.id == _this.user.id  && value.pivot.accept == 1){
              _this.checkConnected = 1;
            }else if(value.id == _this.user.id  && value.pivot.accept == 0){
              _this.checkConnected = 2
            }else {
              _this.checkConnected = 3
            }
          });
        }
      },


      getUser() {
        let _this = this;
        axios.get(apiRoute + '/user',  this.$store.getters['auth/token']).then(response => {
          _this.user = response.data.data;
        }).catch(error => {

        });
      },

      getTeams() {
        let _this = this
        axios.get(apiRoute + '/user/profiles/'+this.profileId+'/teams', this.$store.getters['auth/token']).then(response => {
          _this.allTeams = response.data.data
        }).catch(error => {

        })
      },


    },

    created () {
      this.profileId = this.$route.params.profileId;
      this.getProfile();
      this.getUser();
      this.getTeams();

    },

    mounted() {
      this.updateProfilesColumn(this.profileId);
    },
  };
</script>
<style scoped lang="scss">
  .profile-team-mobile {
    margin: 0 20px;
    .info-header {
      display: flex;
      justify-content: space-between;
      position: relative;
      .back-button,
      .link-edit-team {
        text-decoration: none;
        color: #fff;
        font-size: 1.2rem;
        transition: 0.3s;
        &:hover {
          color: #9d72ff;
          transition: 0.3s;
        }
      }
      .title {
        font-size: 1.5rem;
        font-family: EncodeSansRegular;
      }
    }
    .name-image {
      text-align: center;
      img {
        border-radius: 50%;
        width: 105px;
      }
      .name {
        font-size: 2rem;
        font-family: EncodeSansSemiBold;
      }
      .creative-title {
        font-size: 1.3rem;
      }
    }
    .bio-text {
      font-size: 1.1rem;
      text-align: justify;
    }
  }
  .team-members {
    margin-top: 20px;

    display: flex;
    flex-flow: row wrap;
    .team-member {
      text-align: center;
      width: 50%;
      margin-bottom: 30px;
      img {
        width: 105px;
        border-radius: 50%;
      }
      a {
        color: #fff;
        text-decoration: none;
        transition: 0.3s;
        &:hover {
          color: #9d72ff;
          transition: 0.3s;
        }
        .name {
          margin-top: 5px;
          display: block;
          font-size: 1rem;
        }
        .creative-title {
          display: block;
          font-size: 0.9rem;
        }
      }
    }
  }

  .btn-profile-link{
    color: white;
  }
</style>
