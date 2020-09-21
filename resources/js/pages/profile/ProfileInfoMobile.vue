<template>
  <div class="profile-info-mobile">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="info-header mt-3">
            <router-link class="back-button float-left" :to="{ path: '/profile-detail/'+profileId}">
              <i class="fas fa-arrow-left"></i>
            </router-link>
            <div class="title">INFO</div>
          </div>
          <div class="name-image mt-5">
            <img v-if="profile.avatarUri" :src="urlProfileImage" />
            <img v-else src="/images/user8-128x128.png" />
            <div
              class="name"
              v-if="profile.user"
            >{{profile.user.firstName}} {{profile.user.lastName}}</div>
            <div class="creative-title">{{profile.creativeTitle}}</div>
            <!-- <div class="name">{{profile.creativeTitle}}</div>
            <div class="creative-title">Visual artist</div>-->
          </div>
          <div class="bio-text mt-5" v-html="profileBiography"></div>
          <div class="social-media">
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
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { helpers } from "../../mixins/helpers";
export default {
  name: "ProfileInfoTeam",
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
      user: "",
      events: {},
      checkFollow: false,
      followsCount: 0,
      allTeams: [],
      checkConnected: 3,
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
          _this.urlBackgroundImage =
            "/storage/profiles/profileBackgroundImage/" +
            response.data.data.id +
            "/" +
            response.data.data.backgroundUri;
          if (response.data.data.biography.includes("href=")) {
            let projectBio = response.data.data.biography.replace(
              'href="',
              'target="_blank" href='
            );
            this.profileBiography = projectBio.replace(/["']/g, "");
          } else {
            this.profileBiography = response.data.data.biography;
          }
        })
        .catch(error => {});
    },

    checkUserFollow(follow) {
      let _this = this;
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

    openUrl(url) {
      window.open(url, "_blank");
    },

    getUser() {
      let _this = this;
      axios
        .get(apiRoute + "/user", this.$store.getters["auth/token"])
        .then(response => {
          _this.user = response.data.data;
        })
        .catch(error => {});
    }
  },

  created() {
    this.profileId = this.$route.params.profileId;
    this.getProfile();
    this.getUser();
  },

  mounted() {
    this.updateProfilesColumn(this.profileId);
  }
};
</script>
<style scoped lang="scss">
.profile-info-mobile {
  margin: 0 20px;
  .info-header {
    text-align: center;
    position: relative;
    .back-button {
      position: absolute;
      top: 0px;
      left: 0px;
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
  .social-media {
    padding: 20px 0px;
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
}
</style>
