<template>
  <div
    class="project-detail"
    :style="{background: `linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 1)), url('${urlBackgroundImage}')`}"
  >
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-8 mt-5">
          <div class="row">
            <!-- Desktop header starts-->
            <div class="col project-header d-none d-md-block">
              <div class="image-info">
                <img
                  class="project-avatar"
                  v-if="project.avatarUri"
                  :src="'/storage/projects/projectAvatar/' + projectId + '/' + project.avatarUri"
                />
                <img class="project-avatar" v-else src="/images/user8-128x128.png" />
                <div class="info">
                  <div class="project-name">{{project.name}}</div>
                  <div class="created-by" v-if="user.id != project.userId && project.user">
                    Created by:
                    <router-link
                      :to="{ path: '/profile-detail/'+project.profileId}"
                    >{{project.user.firstName}} {{project.user.lastName}}</router-link>
                  </div>
                </div>
              </div>
            </div>
            <!-- Desktop header ends-->
            <!-- Mobile header starts-->
            <div class="col mobile-project-header d-block d-md-none">
              <div class="info">
                <div class="avatar">
                  <img
                    class="project-avatar"
                    v-if="project.avatarUri"
                    :src="'/storage/projects/projectAvatar/' + projectId + '/' + project.avatarUri"
                  />
                  <img class="project-avatar" v-else src="/images/user8-128x128.png" />
                </div>
                <div class="project-name">{{project.name}}</div>
                <div class="created-by" v-if="user.id != project.userId && project.user">
                  Created by:
                  <router-link
                    :to="{ path: '/profile-detail/'+project.profileId}"
                  >{{project.user.firstName}} {{project.user.lastName}}</router-link>
                </div>
              </div>
            </div>
            <!-- Mobile header ends-->
          </div>
          <!-- Mobile navigation starts -->
          <div class="row project-nav d-block d-md-none">
            <div class="col mt-5 text-center">
              <router-link
                class="btn"
                :to="{ path: '/project-detail/project/'+projectId+'/project/info'}"
              >Info</router-link>
              <router-link
                class="btn"
                v-if="user.id == project.userId && allTeams.length > 0"
                :to="{ path: '/project-detail/project/'+projectId+'/project/team'}"
              >Team</router-link>
              <router-link
                class="btn btn-add-team"
                v-if="user.id == project.userId && allTeams.length == 0"
                :to="{ path: '/project/team/'+projectId}"
              >Add Team</router-link>
            </div>
          </div>
          <!-- Mobile navigation ends -->

          <!-- Project news area starts -->
          <div class="row project-news">
            <div class="col">
              <NewsFeed v-bind:newsList="newsList"></NewsFeed>
              <ModalConfirmation v-if="showConfModal" @close="showConfModal = false">
                <div slot="body">Are you sure, you want to delete this {{typeofContent}}?</div>
              </ModalConfirmation>

              <div
                class="project-empty-content mt-5"
                v-if="createAction &&  user.id == project.userId"
              >
                <div class="line bolder">Your feed is empty.</div>
                <div class="line">Create your first post or poll!</div>
                <button class="btn" v-on:click="$router.push('/posts/create')">Add first post</button>
                <button class="btn" v-on:click="$router.push('/polls/create')">Add poll</button>
              </div>

              <div
                class="project-empty-content mt-5"
                v-if="createAction == true && user.id != project.userId"
              >
                <div class="line bolder">This project does not have any content yet.</div>
              </div>
            </div>
          </div>
          <!-- Project news area ends -->
        </div>

        <!-- Desktop side info starts -->
        <div class="col-4 d-none d-md-block">
          <div class="gradient-block"></div>
          <div class="project-info">
            <div class="bio">
              <span class="title">BIO</span>
              <span class="title-text" v-html="projectDescription"></span>
            </div>

            <div class="team" v-if="user.id == project.userId">
              <div class="d-flex justify-content-between">
                <div class="title">TEAM</div>
                <router-link
                  v-if="user.id == project.userId && allTeams.length > 0"
                  class="edit-team"
                  :to="{ path: '/project/team/'+projectId}"
                >Edit Team</router-link>
              </div>
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

              <div
                class="no-team-memebers"
                v-if="user.id == project.userId && allTeams.length  == 0"
              >
                There are no team members yet!
                <router-link
                  class="btn btn-add-team"
                  :to="{ path: '/project/team/'+projectId}"
                >Add team</router-link>
              </div>
            </div>

            <div class="team" v-if="user.id != project.userId && allTeams.length > 0">
              <div class="d-flex justify-content-between">
                <div class="title">TEAM</div>
                <router-link
                  v-if="user.id == project.userId && allTeams.length > 0"
                  class="edit-team"
                  :to="{ path: '/project/team/'+projectId}"
                >Edit Team</router-link>
              </div>
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

              <div
                class="no-team-memebers"
                v-if="user.id == project.userId && allTeams.length  == 0"
              >
                There are no team members yet!
                <router-link
                  class="btn btn-add-team"
                  :to="{ path: '/project/team/'+projectId}"
                >Add team</router-link>
              </div>
            </div>
          </div>
        </div>
        <!-- Desktop side info ends -->
      </div>
    </div>
    <div class="loading" v-show="showLoader">
      <span class="fa fa-spinner fa-spin"></span>
    </div>
  </div>
</template>
<script>
import ModalConfirmation from "../../shared/modals/ModalConfirmation.vue";
import NewsFeed from "../../shared/newsfeed/NewsFeed.vue";
export default {
  name: "ProjectDetail",
  middleware: "auth",
  components: {
    ModalConfirmation,
    NewsFeed
  },
  data: function() {
    return {
      newsList: [],
      typeofContent: "",
      showConfModal: false,
      profileId: "",
      projectId: "",
      urlBackgroundImage: "",
      profiles: {},
      socialMediaLinks: {},
      posts: {},
      user: {},
      project: {},
      allTeams: [],
      createAction: false,
      showLoader: false,
      type: "project",
      notificationId: "",
      projectDescription: ""
    };
  },

  methods: {
    getProject() {
      this.newsList = [];
      let _this = this;
      this.showLoader = true;
      axios
        .get(apiRoute + "/user/projects/" + this.projectId)
        .then(response => {
          _this.project = response.data.data;

          if (response.data.data.description.includes("href=")) {
            let projectDesc = response.data.data.description.replace(
              'href="',
              'target="_blank" href='
            );
            this.projectDescription = projectDesc.replace(/["']/g, "");
          } else {
            this.projectDescription = response.data.data.description;
          }

          if (
            !response.data.data.media.length &&
            !response.data.data.polls.length &&
            !response.data.data.posts.length
          ) {
            this.createAction = true;
          } else {
            this.createAction = false;
          }

          response.data.data.media.forEach((value, key) => {
            _this.newsList.push({
              id: value.id,
              userId: value.userId,
              fieldName: value.fieldName,
              displayName: value.displayName,
              uri: value.uri,
              hudType: value.fieldName,
              projectId: this.projectId,
              createdAt: value.createdAt
            });
          });

          response.data.data.polls.forEach((value, key) => {
            _this.newsList.push({
              id: value.id,
              userId: value.userId,
              name: value.name,
              question: value.question,
              userPollAnswer: value.userPollAnswer,
              projectId: value.projectId,
              profileId: value.profileId,
              likes: value.likes,
              likeCount: value.likeCount,
              hudType: value.hudType,
              answerType: value.answerType,
              answer: value.answer,
              answers: value.answers,
              comments: value.comments,
              commentCount: value.commentCount,
              createdAt: value.createdAt,
              updatedAt: value.updatedAt
            });
          });

          response.data.data.posts.forEach((value, key) => {
            _this.newsList.push({
              id: value.id,
              userId: value.userId,
              summary: value.summary,
              shareCount: value.shareCount,
              projectId: value.projectId,
              profileId: value.profileId,
              likes: value.likes,
              likeCount: value.likeCount,
              imageUri: value.imageUri,
              images: value.images,
              links: value.links,
              hudType: value.hudType,
              description: value.description,
              comments: value.comments,
              commentCount: value.commentCount,
              createdAt: value.createdAt,
              updatedAt: value.updatedAt
            });
          });

          if (response.data.data.backgroundUri) {
            _this.urlBackgroundImage =
              "/storage/projects/projectBackgroundImg/" +
              response.data.data.id +
              "/" +
              response.data.data.backgroundUri;
          } else {
            _this.urlBackgroundImage = "/images/cover-image.png";
          }

          this.showLoader = false;
        })
        .catch(function(error) {
          this.showLoader = false;
        });
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
        })
        .catch(error => {});
    },

    deleteProjectMedia(id) {
      let _this = this;
      axios
        .delete(apiRoute + "/user/projects/" + this.projectId + "/media/" + id)
        .then(response => {
          $("#modal-remove-media-" + id).modal("hide");
          this.getProject();
        })
        .catch(function(error) {});
    },

    dateChange: function(date) {
      return moment(date).format("MMM DD");
    },

    updateProjectColumn() {
      axios
        .get(
          apiRoute + "/user/projects/column/" + this.projectId + "/update",
          this.$store.getters["auth/token"]
        )
        .then(response => {})
        .catch(error => {});
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
    this.projectId = this.$route.params.projectId;
    this.notificationId = this.$route.params.notificationId;
    bus.$on("refresh", data => {
      if (data) {
        this.getProject();
      }
    });
  },

  mounted() {
    $(".dropdown-menu-notification").removeClass("show");

    if (this.notificationId) {
      this.updateNotification();
      bus.$emit("notRefresh", 1);
    }

    this.$root.$on("confirmation modal", openModal => {
      this.showConfModal = openModal.open;
      this.typeofContent = openModal.contentType;
    });

    this.getProject();
    this.getUser();
    this.getTeams();
    this.updateProjectColumn();
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

.project-empty-content {
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
.project-detail {
  /*background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 1)),*/
  /*url("/images/profile-cover-lighter.jpeg");*/
  background-repeat: no-repeat !important;
  background-position: center top !important;
  background-size: 180% auto !important;
  @media (max-width: 767px) {
    background-size: auto;
  }
}
/* Mobile header style */
.mobile-project-header {
  margin-top: 50px;
  position: relative;
  background-color: #000;
  border-top-left-radius: 130px;
  border-top-right-radius: 130px;
  text-align: center;
  height: 105px;
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
    .project-name {
      font-size: 2rem;
      font-family: EncodeSansSemiBold;
    }
  }
}

/* Desktop  header style */
.project-header {
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

    .project-avatar {
      width: 140px;
      border-radius: 50%;
      margin-right: 30px;
    }
    .project-name {
      font-size: 2rem;
      font-family: EncodeSansSemiBold;
    }
  }
}
.project-nav {
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
        width: 120px;
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
.project-info {
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
}
.team {
  padding: 20px 30px;
  border-top: 2px #000 solid;
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
</style>
