<template>
  <div class="project-info-mobile">
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
            <div class="title">INFO</div>
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
          <div class="bio-text mt-5" v-html="project.description"></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "ProjectInfoMobile",
  middleware: "auth",
  components: {},
  data: function() {
    return {
      showModal: false,
      profileId: "",
      projectId: "",
      project: {},
      user: {}
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
    // this.profileId = this.$route.params.profileId
    this.projectId = this.$route.params.projectId;
  },

  mounted() {
    this.getProject();
  }
};
</script>
<style scoped lang="scss">
.project-info-mobile {
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
  }
  .bio-text {
    font-size: 1.1rem;
    text-align: justify;
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
}
</style>
