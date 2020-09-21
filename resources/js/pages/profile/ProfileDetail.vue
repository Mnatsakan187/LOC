<template>
  <div
    class="profile-detail"
    :style="{background: `linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 1)), url('${urlBackgroundImage}')`}"
  >
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-8 mt-5">
          <div class="row">
            <!-- Desktop header starts-->
            <div class="col profile-header d-none d-md-block">
              <div class="image-info">
                <img class="profile-avatar" v-if="profile.avatarUri" :src="urlProfileImage" />
                <img class="profile-avatar" v-else src="/images/user8-128x128.png" />
                <div class="info">
                  <div
                    class="profile-name"
                    v-if="profile.user"
                  >{{profile.user.firstName}} {{profile.user.lastName}}</div>
                  <div class="creative-title">{{profile.creativeTitle}}</div>
                  <template>
                    <div class="profile-actions" v-if="checkAuthUser == 0">
                      <div class="follow mt-3">
                        <button
                          v-if="checkFollow"
                          @click="followFunction('delete')"
                          class="btn btn-following"
                        >
                          <i class="fas fa-check"></i> Following
                        </button>
                        <button
                          v-else
                          @click="followFunction('store')"
                          class="btn btn-follow"
                        >Follow</button>
                        <a
                          class="followers-link"
                          href="javascript:void(0)"
                          @click="showModal = true"
                          v-if="followsCount > 0"
                        >
                          <span v-if="followsCount == 1">
                            <i class="fas fa-user"></i>
                            {{followsCount}} follower
                          </span>
                          <span v-else>
                            <i class="fas fa-user"></i>
                            {{followsCount}} followers
                          </span>
                        </a>
                      </div>

                      <div
                        class="connect"
                        v-if="user.accountType == 1 || (user.accountType == 0 && profile.user.accountType == 0) "
                      >
                        <button
                          v-if="checkConnected == 1"
                          @click="connect('delete')"
                          class="btn btn-connected"
                        >
                          <i class="fas fa-check"></i> Connected
                        </button>

                        <button v-else-if="checkConnected == 2" class="btn btn-connect">Connect Sent</button>

                        <button v-else @click="connect('store')" class="btn btn-connect">Connect</button>
                      </div>
                      <ModalMyFollowers
                        :followers="profile.follows"
                        v-if="showModal"
                        @close="showModal = false"
                      ></ModalMyFollowers>
                    </div>

                    <div v-else class="profile-actions">
                      <a
                        class="followers-link"
                        href="javascript:void(0)"
                        @click="showModal = true"
                        v-if="followsCount > 0"
                      >
                        <span v-if="followsCount == 1">
                          <i class="fas fa-user"></i>
                          {{followsCount}} follower
                        </span>
                        <span v-else>
                          <i class="fas fa-user"></i>
                          {{followsCount}} followers
                        </span>
                      </a>

                      <ModalMyFollowers
                        :followers="profile.follows"
                        v-if="showModal"
                        @close="showModal = false"
                      ></ModalMyFollowers>
                    </div>
                  </template>
                </div>
              </div>
            </div>
            <!-- Desktop header ends-->
            <!-- Mobile header starts-->
            <div class="col mobile-profile-header d-block d-md-none">
              <div class="info">
                <div class="avatar">
                  <img class="profile-avatar" v-if="profile.avatarUri" :src="urlProfileImage" />
                  <img class="profile-avatar" v-else src="/images/user8-128x128.png" />
                </div>
                <div class="profile-name">{{user.firstName}} {{user.lastName}}</div>
                <div class="creative-title">{{profile.creativeTitle}}</div>
              </div>

              <template>
                <div v-if="checkAuthUser == 0">
                  <div class="follow mt-2">
                    <button
                      v-if="checkFollow"
                      @click="followFunction('delete')"
                      class="btn btn-following"
                    >
                      <i class="fas fa-check"></i> Following
                    </button>
                    <button v-else @click="followFunction('store')" class="btn btn-follow">Follow</button>
                    <a
                      class="followers-link"
                      href="javascript:void(0)"
                      @click="showModal = true"
                      v-if="followsCount > 0"
                    >
                      <span v-if="followsCount == 1">
                        <i class="fas fa-user"></i>
                        {{followsCount}} follower
                      </span>
                      <span v-else>
                        <i class="fas fa-user"></i>
                        {{followsCount}} followers
                      </span>
                    </a>
                  </div>

                  <div class="connect">
                    <button
                      v-if="checkConnected == 1"
                      @click="connect('delete')"
                      class="btn btn-connected"
                    >
                      <i class="fas fa-check"></i> Connected
                    </button>

                    <button v-else-if="checkConnected == 2" class="btn btn-connect">Connect Sent</button>

                    <button v-else @click="connect('store')" class="btn btn-connect">Connect</button>
                  </div>
                  <ModalMyFollowers
                    :followers="profile.follows"
                    v-if="showModal"
                    @close="showModal = false"
                  ></ModalMyFollowers>
                </div>

                <div v-else>
                  <a
                    class="followers-link"
                    href="javascript:void(0)"
                    @click="showModal = true"
                    v-if="followsCount > 0"
                  >
                    <span v-if="followsCount == 1">
                      <i class="fas fa-user"></i>
                      {{followsCount}} follower
                    </span>
                    <span v-else>
                      <i class="fas fa-user"></i>
                      {{followsCount}} followers
                    </span>
                  </a>
                  <ModalMyFollowers
                    :followers="profile.follows"
                    v-if="showModal"
                    @close="showModal = false"
                  ></ModalMyFollowers>
                </div>
              </template>
            </div>
            <!-- Mobile header ends-->
          </div>
          <!-- Desktop navigation starts -->
          <div class="row profile-nav d-none d-md-block">
            <div class="col mt-5">
              <router-link
                class="btn"
                v-if="user.accountType == 1"
                :to="{ path: '/profile-detail/'+profileId}"
              >News</router-link>
              <router-link
                v-if="user.accountType == 1"
                class="btn"
                :to="{ path: '/profile-detail/'+profileId+'/profile/projects'}"
              >Projects</router-link>
            </div>
          </div>
          <!-- Desktop navigation ends -->
          <!-- Mobile navigation starts -->
          <div class="row profile-nav d-block d-md-none">
            <div class="col mt-5 text-center">
              <router-link
                class="btn"
                :to="{ path: '/profile-detail/'+profileId+'/profile/info'}"
              >Info</router-link>
              <router-link
                v-if="user.accountType == 1"
                class="btn"
                :to="{ path: '/profile-detail/'+profileId+'/profile/projects'}"
              >Projects</router-link>
              <router-link
                v-if="user.accountType == 1 && allTeams.length > 0"
                class="btn"
                :to="{ path: '/profile-detail/'+profileId+'/profile/team'}"
              >Team</router-link>
              <router-link
                class="btn btn-add-team"
                v-if="user.accountType == 1 &&  user.id == profile.userId && allTeams.length == 0"
                :to="{ path: '/profile/team/'+profileId}"
              >Add Team</router-link>
            </div>
          </div>
          <!-- Mobile navigation ends -->
          <router-view />
        </div>

        <!-- Desktop side info starts -->
        <div class="col-4 d-none d-md-block">
          <div class="gradient-block"></div>
          <div class="profile-info">
            <div class="bio">
              <span class="title">BIO</span>
              <span class="title-text title-text-biography" v-html="profileBiography"></span>
            </div>
            <div
              v-if="user.accountType == 1 && socialMediaLinks.length"
              class="social-media"
              v-bind:class="{ borderSize: allTeams.length == 0 }"
            >
              <span class="title">FIND ME IN</span>
              <div class="icons">
                <a
                  v-for="socialLInk in socialMediaLinks"
                  @click="openUrl(socialLInk.socialMediaLink)"
                  target="_blank"
                >
                  <i v-if="socialLInk.name == 'square'" :class="'fas fa-rss-'+socialLInk.name"></i>
                  <i v-else :class="'fab fa-'+socialLInk.name"></i>
                </a>
              </div>
            </div>

            <div v-if="user.accountType == 0 && socialMediaLinks.length" class="social-media-user">
              <span class="title">FIND ME IN</span>
              <div class="icons">
                <a
                  v-for="socialLInk in socialMediaLinks"
                  @click="openUrl(socialLInk.socialMediaLink)"
                  target="_blank"
                >
                  <i v-if="socialLInk.name == 'square'" :class="'fas fa-rss-'+socialLInk.name"></i>
                  <i v-else :class="'fab fa-'+socialLInk.name"></i>
                </a>
              </div>
            </div>

            <div
              v-if="user.accountType == 1 && user.id == profile.userId"
              class="team"
              v-bind:class="{ borderBottom: socialMediaLinks.length == 0 }"
            >
              <div class="d-flex justify-content-between">
                <div class="title">TEAM</div>
                <router-link
                  v-if="user.id == profile.userId && allTeams.length > 0"
                  class="edit-team"
                  :to="{ path: '/profile/team/'+profileId}"
                >Edit Team</router-link>
              </div>
              <div class="team-members">
                <div class="team-member" v-for="team in allTeams">
                  <img
                    v-if="team.avatarUri"
                    :src="'/storage/avatarImage/' + team.id + '/' + team.avatarUri"
                    alt="default"
                  />
                  <img v-else src="/images/user8-128x128.png" alt="default" />
                  <router-link :to="{ path: '/profile-detail/'+team.id}" target="_blank">
                    <span class="name">{{team.creativeTitle}}</span>
                    <span class="creative-title" v-if="team.contentPreferenceWritten == 1">Written</span>
                    <span class="creative-title" v-if="team.contentPreferenceAudio  == 1">Audio</span>
                    <span class="creative-title" v-if="team.contentPreferenceVisual == 1">Visual</span>
                    <span class="creative-title" v-if="team.contentPreferenceEvents == 1">Events</span>
                  </router-link>
                </div>
              </div>
              <div
                class="no-team-memebers"
                v-if="user.id == profile.userId && allTeams.length  == 0"
              >
                There are no team members yet!
                <router-link
                  class="btn btn-add-team"
                  :to="{ path: '/profile/team/'+profileId}"
                >Add team</router-link>
              </div>
            </div>

            <div
              v-if="user.accountType == 1 && user.id != profile.userId && allTeams.length"
              class="team"
            >
              <div class="d-flex justify-content-between">
                <div class="title">TEAM</div>
                <router-link
                  v-if="user.id == profile.userId && allTeams.length > 0"
                  class="edit-team"
                  :to="{ path: '/profile/team/'+profileId}"
                >Edit Team</router-link>
              </div>
              <div class="team-members">
                <div class="team-member" v-for="team in allTeams">
                  <img
                    v-if="team.avatarUri"
                    :src="'/storage/avatarImage/' + team.id + '/' + team.avatarUri"
                    alt="default"
                  />
                  <img v-else src="/images/user8-128x128.png" alt="default" />
                  <router-link :to="{ path: '/profile-detail/'+team.id}" target="_blank">
                    <span class="name">{{team.creativeTitle}}</span>
                    <span class="creative-title" v-if="team.contentPreferenceWritten == 1">Written</span>
                    <span class="creative-title" v-if="team.contentPreferenceAudio  == 1">Audio</span>
                    <span class="creative-title" v-if="team.contentPreferenceVisual == 1">Visual</span>
                    <span class="creative-title" v-if="team.contentPreferenceEvents == 1">Events</span>
                  </router-link>
                </div>
              </div>
              <div
                class="no-team-memebers"
                v-if="user.id == profile.userId && allTeams.length  == 0"
              >
                There are no team members yet!
                <router-link
                  class="btn btn-add-team"
                  :to="{ path: '/profile/team/'+profileId}"
                >Add team</router-link>
              </div>
            </div>
          </div>
        </div>
        <!-- Desktop side info ends -->
      </div>
    </div>
  </div>
</template>
<script>
import ModalMyFollowers from "../../shared/modals/ModalMyFollowers.vue";
import { helpers } from "../../mixins/helpers";
export default {
  name: "ProfileDetail",
  components: {
    ModalMyFollowers
  },
  middleware: "auth",
  mixins: [helpers],
  data: function() {
    return {
      showModal: false,
      profile: {},
      urlProfileImage: "",
      urlBackgroundImage: "",
      backgroundShow: true,
      socialMediaLinks: {},
      posts: {},
      user: {
        accountType: 1
      },
      events: {},
      checkFollow: false,
      followsCount: 0,
      allTeams: [],
      checkConnected: 3,
      notificationId: "",
      checkAuthUser: 1,
      profileBiography: ""
    };
  },

  methods: {
    getProfile() {
      let _this = this;
      axios
        .get(
          apiRoute + "/user/profiles/" + this.profileId,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.profile = response.data.data;
          _this.checkUserFollow(response.data.data.follows);
          _this.checkConnect(response.data.data.connection);
          _this.socialMediaLinks = response.data.data.socialMediaLinks;
          _this.urlProfileImage =
            "/storage/profiles/profilePictureImage/" +
            response.data.data.id +
            "/" +
            response.data.data.avatarUri;

          if (response.data.data.backgroundUri) {
            _this.urlBackgroundImage =
              "/storage/profiles/profileBackgroundImage/" +
              response.data.data.id +
              "/" +
              response.data.data.backgroundUri;
          } else {
            _this.urlBackgroundImage = "/images/cover-image.png";
          }

          if (response.data.data.biography.includes("href=")) {
            let projectBio = response.data.data.biography.replace(
              'href="',
              'target="_blank" href='
            );
            this.profileBiography = projectBio.replace(/["']/g, "");
          } else {
            this.profileBiography = response.data.data.biography;
          }

          if (this.user.id != response.data.data.userId) {
            this.checkAuthUser = 0;
          } else {
            this.checkAuthUser = 1;
          }
        })
        .catch(error => {});
    },

    checkUserFollow(follow) {
      let _this = this;
      _this.checkFollow = false;
      if (follow != undefined) {
        _this.followsCount = follow.length;
        follow.forEach((value, key) => {
          if (value.id == _this.user.id) {
            _this.checkFollow = true;
          }
        });
      }
    },

    checkConnect(connected) {
      let _this = this;
      if (connected != undefined) {
        connected.forEach((value, key) => {
          if (value.id == _this.user.id && value.pivot.accept == 1) {
            _this.checkConnected = 1;
          } else if (value.id == _this.user.id && value.pivot.accept == 0) {
            _this.checkConnected = 2;
          } else {
            _this.checkConnected = 3;
          }
        });
      }
    },

    connect(type) {
      let _this = this;
      if (type == "store") {
        axios
          .post(
            apiRoute + "/user/connection/" + this.profileId,
            this.$store.getters["auth/token"]
          )
          .then(response => {
            _this.getProfile();
          })
          .catch(error => {});
      } else {
        axios
          .delete(
            apiRoute + "/user/connection/" + this.profileId,
            this.$store.getters["auth/token"]
          )
          .then(response => {
            _this.checkConnected = 3;
            _this.getProfile();
          })
          .catch(error => {});
      }
    },

    openUrl(url) {
      window.open(url, "_blank");
    },

    getUser() {
      let _this = this;
      axios
        .get(apiRoute + "/user", this.$store.getters["auth/token"])
        .then(response => {
          _this.user = response.data.data;
          this.getProfile();
        })
        .catch(error => {});
    },

    getTeams() {
      let _this = this;
      axios
        .get(
          apiRoute + "/user/profiles/" + this.profileId + "/teams",
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.allTeams = response.data.data;
        })
        .catch(error => {});
    },

    followFunction(type) {
      let _this = this;
      if (type == "store") {
        axios
          .post(
            apiRoute + "/user/follows/" + this.profileId,
            this.$store.getters["auth/token"]
          )
          .then(response => {
            _this.getProfile();
          })
          .catch(error => {});
      } else {
        axios
          .delete(
            apiRoute + "/user/follows/" + this.profileId,
            this.$store.getters["auth/token"]
          )
          .then(response => {
            _this.getProfile();
          })
          .catch(error => {});
      }
    },

    updateNotification() {
      axios
        .get(
          apiRoute + "/notifications/read/" + this.notificationId,
          this.$store.getters["auth/token"]
        )
        .then(response => {})
        .catch(error => {});
    }
  },

  created() {
    this.profileId = this.$route.params.profileId;
    this.notificationId = this.$route.params.notificationId;
    this.getUser();
    this.updateProfilesColumn(this.profileId);
  },

  mounted() {
    $(".dropdown-menu-notification").removeClass("show");
    if (this.notificationId) {
      this.updateNotification();
      bus.$emit("notRefresh", 1);
    }
    this.getTeams();
  }
};
</script>
<style lang="scss">
.edit-team {
  color: #fff;
  transition: 0.3s;
  font-size: 1rem;
  text-decoration: none;
  &:hover {
    color: #9d72ff;
    transition: 0.3s;
    text-decoration: none;
  }
}

.profile-empty-content {
  text-align: center;
  .line {
    padding-bottom: 20px;
    color: #fff;
    font-family: EncodeSansRegular;
    font-size: 1.2rem;
    &.bolder {
      font-family: EncodeSansSemiBold;
      font-size: 1.6rem;
    }
  }
  .btn {
    margin: 0 auto;
    display: block;
    color: #fff;
    border: 1px solid #fff;
    border-radius: 24px;
    font-size: 1.2rem;
    transition: 0.5s;
    margin-bottom: 20px;
    padding: 10px 20px;
    &:hover {
      color: #9d72ff;
      border: 1px solid #9d72ff;
      transition: 0.5s;
    }
  }
}
</style>
<style scoped lang="scss">
.profile-detail {
  background-repeat: no-repeat !important;
  background-position: center top !important;
  background-size: 180% auto !important;
  @media (max-width: 767px) {
    background-size: auto;
  }
}

/* Mobile header style */
.mobile-profile-header {
  margin-top: 50px;
  position: relative;
  background-color: #000;
  border-top-left-radius: 130px;
  border-top-right-radius: 130px;
  text-align: center;
  height: 140px;
  .info {
    margin-top: 40px;
    .avatar {
      position: absolute;
      top: -70px;
      left: 50%;
      transform: translate(-50%, 0);
      img {
        border-radius: 50%;
        width: 105px;
      }
    }
    .profile-name {
      font-size: 2rem;
      font-family: EncodeSansSemiBold;
    }
    .creative-title {
      font-size: 1.3rem;
    }
  }
}

/* Desktop  header style */
.profile-header {
  position: relative;
  .overlay {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100%;
    width: 100%;
    opacity: 0.7;
    background: rgb(68, 68, 68);
    background: linear-gradient(
      180deg,
      rgba(68, 68, 68, 1) 0%,
      rgba(0, 0, 0, 1) 100%
    );
  }
  .image-info {
    color: #fff;
    display: flex;
    align-items: center;

    .profile-avatar {
      width: 140px;
      border-radius: 50%;
      margin-right: 30px;
    }
    .profile-name {
      font-size: 2rem;
      font-family: EncodeSansSemiBold;
    }
    .creative-title {
      font-size: 1.2rem;
    }
  }
}

.profile-nav {
  .btn {
    color: #fff;
    text-transform: uppercase;
    border: 1px solid #fff;
    border-radius: 20px;
    width: 130px;
    margin-right: 10px;
    transition: 0.3s;
    @media (max-width: 767px) {
      width: 100px;
      &.btn-add-team {
        width: 106px;
      }
    }
    &:hover {
      color: #9d72ff;
      border: 1px solid #9d72ff;
      transition: 0.3s;
    }
    &.router-link-exact-active {
      color: #000;
      background: #fff;
      cursor: default;
    }
  }
}

.gradient-block {
  height: 270px;
  background: linear-gradient(rgba(26, 26, 26, 0.4), rgba(26, 26, 26, 1));
  margin: 0 10px;
  max-width: 350px;
  min-width: 230px;
}

.profile-info {
  background-color: #1a1a1a;
  padding: 20px 0;
  margin: 0 10px;
  max-width: 350px;
  min-width: 230px;

  .title {
    display: block;
  }
  .bio {
    padding: 20px 30px;
    .title-text {
      display: block;
      margin-top: 10px;
    }
  }
  .social-media {
    padding: 20px 30px;
    border-top: 2px solid #000;
    border-bottom: 2px solid #000;
    .icons {
      padding-top: 20px;
      display: flex;
      flex-flow: row wrap;
      a {
        width: 25%;
        text-align: center;
        color: #fff;
        text-decoration: none;
        opacity: 1;
        transition: 0.3s;
        font-size: 1.8rem;
        margin-bottom: 10px;
        &:hover {
          opacity: 0.6;
          transition: 0.3s;
        }
      }
    }
  }

  .social-media-user {
    padding: 20px 30px;
    border-top: 2px solid #000;
    .icons {
      padding-top: 20px;
      display: flex;
      flex-flow: row wrap;
      a {
        width: 25%;
        text-align: center;
        color: #fff;
        text-decoration: none;
        opacity: 1;
        transition: 0.3s;
        font-size: 1.8rem;
        margin-bottom: 10px;
        &:hover {
          opacity: 0.6;
          transition: 0.3s;
        }
      }
    }
  }
}

.team {
  padding: 20px 30px;
  .team-members {
    margin-top: 20px;
    display: flex;
    flex-flow: row wrap;
    .team-member {
      width: 45%;
      margin-bottom: 30px;
      img {
        width: 60px;
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
}

/* Follow / connect buttons styles */
.btn-follow,
.btn-connect {
  background: #fff;
  color: #000;
  opacity: 1;
  transition: 0.5s;
  min-width: 120px;
  border-radius: 20px;
  margin-right: 10px;
  margin-bottom: 10px;
  &:hover {
    opacity: 0.7;
    transition: 0.5s;
  }
}

.btn-following,
.btn-connected {
  background: #302f30;
  color: #fff;
  opacity: 0.7;
  transition: 0.5s;
  min-width: 120px;
  border-radius: 20px;
  margin-right: 10px;
  margin-bottom: 10px;
  &:hover {
    opacity: 0.9;
    transition: 0.5s;
  }
}

/* Followers link */
.followers-link {
  color: #fff;
  transition: 0.3s;
  vertical-align: super;
  text-decoration: none;
  text-transform: uppercase;
  font-size: 0.8rem;
  &:hover {
    color: #9d72ff;
    transition: 0.3s;
  }
}

.btn-profile-link {
  color: white;
}

.no-team-memebers {
  text-align: center;
  color: #fff;
  font-size: 1.2rem;
  .btn-add-team {
    margin-top: 10px;
    margin-left: auto;
    margin-right: auto;
    display: block;
    width: 120px;
    color: #fff;
    border-radius: 20px;
    padding: 10px 15px;
    border: 1px solid #fff;
    transition: 0.3s;
    &:hover {
      border: 1px solid #9d72ff;
      color: #9d72ff;
      transition: 0.3s;
    }
  }
}

.borderSize {
  border-bottom: 0px solid #000 !important;
}

.borderBottom {
  padding: 20px 30px;
  border-top: 2px solid #000;
}
</style>
