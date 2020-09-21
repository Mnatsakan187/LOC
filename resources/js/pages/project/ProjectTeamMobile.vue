<template>
  <div class="project-team-mobile">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="info-header mt-3">
            <router-link
              class="back-button float-left"
              :to="{ path: '/project-detail/project/'+projectId}"
            >
              <i class="fas fa-arrow-left"></i>
            </router-link>
            <div class="title">TEAM</div>
            <router-link class="link-edit-team" :to="{ path: '/project/team/'+ projectId}">Edit team</router-link>
          </div>
          <div class="name-image mt-5">
            <img
              v-if="project.avatarUri"
              :src="'/storage/projects/projectAvatar/' + projectId + '/' + project.avatarUri"
            />
            <img v-else src="/images/user8-128x128.png" />
            <div class="name">{{project.name}}</div>
            <div class="created-by" v-if="user.id != project.userId && project.user">
              Created by:
              <router-link
                :to="{ path: '/profile-detail/'+project.profileId}"
              >{{project.user.firstName}} {{project.user.lastName}}</router-link>
            </div>
          </div>
          <div class="team-text mt-5 text-center">
            [They Earn a Mention]
            <br />Check out some of the other Creators who help me do what I do.
          </div>
          <router-link
            v-if="user.id == project.userId && allTeams.length > 0"
            :to="{ path: '/project/team/'+projectId}"
            class="btn btn-profile-link"
          >Edit Team</router-link>
          <router-link
            v-if="user.id == project.userId && allTeams.length  == 0"
            :to="{ path: '/project/team/'+projectId}"
            class="btn btn-profile-link"
          >Add Team</router-link>
          <div class="team-members">
            <div v-for="team in allTeams" class="team-member">
              <img
                v-if="team.avatarUri"
                :src="'/storage/avatarImage/' + team.id + '/' + team.avatarUri"
              />
              <img v-else src="/images/user8-128x128.png" alt="default" />
              <router-link :to="{ path: '/profile-detail/'+team.id}" target="_blank">
                <span class="name">{{team.creativeTitle}}</span>
                <span class="creative-title" v-if="team.contentPreferenceWritten == 1">Written</span>
                <span class="creative-title" v-if="team.contentPreferenceAudio == 1">Audio</span>
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
export default {
  name: "ProjectTeamMobile",
  middleware: "auth",
  components: {},
  data: function() {
    return {
      showModal: false,
      profileId: "",
      projectId: "",
      urlBackgroundImage: "",
      profiles: {},
      socialMediaLinks: {},
      posts: {},
      user: {},
      project: {},
      allTeams: []
    };
  },

  methods: {
    getProject() {
      let _this = this;
      axios
        .get(apiRoute + "/user/projects/" + this.projectId)
        .then(response => {
          _this.project = response.data.data;
          if (response.data.data.backgroundUri) {
            _this.urlBackgroundImage =
              "/storage/projects/projectBackgroundImg/" +
              response.data.data.id +
              "/" +
              response.data.data.backgroundUri;
          }
        })
        .catch(function(error) {});
    },

    getTeams() {
      axios
        .get(
          apiRoute + "/user/projects/team/" + this.projectId + "/teams",
          this.$store.getters["auth/token"]
        )
        .then(response => {
          this.allTeams = response.data.data;
        })
        .catch(error => {});
    },

    getUser() {
      axios
        .get(apiRoute + "/user", this.$store.getters["auth/token"])
        .then(response => {
          this.user = response.data.data;
        })
        .catch(error => {});
    }
  },

  created() {
    this.profileId = this.$route.params.profileId;
    this.projectId = this.$route.params.projectId;
  },

  mounted() {
    this.getProject();
    this.getUser();
    this.getTeams();
  }
};
</script>
<style scoped lang="scss">
.project-team-mobile {
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
.created-by {
  font-size: 1.2rem;
  a {
    color: #fff;
    transition: 0.3s;
    text-decoration: none;
    &:hover {
      color: #9d72ff;
      transition: 0.3s;
    }
  }
}

.btn-profile-link {
  color: white;
}
</style>
