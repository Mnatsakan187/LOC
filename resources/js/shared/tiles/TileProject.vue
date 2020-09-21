<template>
  <div class="tile tile-project">
    <div class="info-dropdown d-flex justify-content-between">
      <div class="info" @click="openUrl(item.id)">
        <span class="title">{{item.name}}</span>
        <span>By {{item.user.firstName + ' '+ item.user.lastName }}</span>
      </div>
      <div class="action-dropdown" v-click-outside="closeActionDropdown">
        <button
          class="btn dd-toggle"
          :class="{'show': collapsed}"
          v-on:click="toggleActionDropdown"
          type="button"
        >
          <i class="fas fa-ellipsis-h"></i>
        </button>
        <div class="action-dropdown-menu" :class="{'show': collapsed}">
          <a
            class="dd-item"
            v-if="routeName == 'my.feed' || routeName == 'discover'"
            @click="hideProject(item.id, 'project')"
          >Hide from HUD</a>
          <a
            v-if="routeName == 'my.feed' || routeName == 'discover' || routeName == 'my.fav.projects'"
            class="dd-item"
            @click="openCollectionModal(item.id, 'projects', item.collections)"
          >Add to collection</a>
          <a
            class="dd-item"
            v-if="routeName=='collection.detail'"
            @click="removeCollectionable(item.id, collectionId, 'projects')"
          >Remove from collection</a>

          <router-link
            v-if="routeName=='profile.projects'"
            class="dropdown-item"
            :to="{ path: '/project-detail/project/'+item.id}"
          >View</router-link>
          <router-link
            class="dd-item"
            v-if="routeName=='profile.projects' && this.isItemCreator"
            :to="{ path: '/project/edit/'+item.id}"
          >Edit</router-link>

          <a
            class="dd-item"
            v-if="routeName=='profile.projects' && this.isItemCreator"
            v-on:click="showModal = true"
            @click="removeProject(item.id, item.profileId)"
          >Delete</a>
        </div>
      </div>
    </div>

    <img
      v-if="item.avatarUri"
      :class="{'updated': item.updatedColor && item.color}"
      :src="'/storage/projects/projectAvatar/' + item.id + '/' + item.avatarUri"
      class="tile-image"
    />
    <img
      v-else
      :class="{'updated': item.updatedColor && item.color}"
      src="/images/project.png"
      class="tile-image"
    />
    <div class="overlay" v-if="item.updatedColor && item.color" :class="getWAVEtype(item.type)"></div>

    <ModalConfirmation v-if="showModal" :deleteId="projectId" :type="type" :profileId="profileId">
      <div slot="body">Are you sure, you want to delete this project?</div>
    </ModalConfirmation>

    <div class="follow-action-buttons d-flex justify-content-between align-items-center">
      <div class="follow-statistics">
        <span class="type-circle">{{getWAVEtype(item.wave)}}</span>
      </div>
      <div class="action-area" v-if="!liked.likeableId">
        <button
          :disabled="disabled"
          v-if="!item.likes"
          @click="like(item.id, false, 0, 'projects')"
        >
          <i class="far fa-heart"></i>
        </button>
        <button :disabled="disabled" v-else @click="like(item.id, true, item.likes.id, 'projects')">
          <i class="fas fa-heart isLike"></i>
        </button>
      </div>

      <div class="action-area" v-else>
        <button
          :disabled="disabled"
          v-if="liked.likeableId != item.id"
          @click="like(item.id, false, 0, 'projects')"
        >
          <i class="far fa-heart"></i>
        </button>
        <button :disabled="disabled" v-else @click="like(item.id, true, liked.id, 'projects')">
          <i class="fas fa-heart isLike"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import ModalConfirmation from "../modals/ModalConfirmation";
export default {
  name: "TileProject",
  components: { ModalConfirmation },
  props: ["item"],
  data() {
    return {
      collapsed: false,
      disabled: false,
      isLike: false,
      collectionId: "",
      showModal: false,
      projectId: "",
      profileId: "",
      type: "",
      liked: {},
      user: {}
    };
  },

  computed: {
    routeName() {
      return this.$route.name;
    },

    isItemCreator() {
      return this.item.userId === this.user.id
    }
  },

  methods: {
    getWAVEtype: function(type) {
      switch (type) {
        case 0:
          return "w";
        case 1:
          return "a";
        case 2:
          return "v";
      }
    },

    openUrl(id) {
      this.$router.push({
        path: "/project-detail/project/" + id
      });
    },

    toggleActionDropdown() {
      this.collapsed = !this.collapsed;
    },
    closeActionDropdown() {
      this.collapsed = false;
    },

    openCollectionModal(collectionableId, type, collections) {
      this.collapsed = false;
      bus.$emit("collection", {
        collectionableId: collectionableId,
        type: type,
        collections: collections
      });
    },

    like(id, like, likeId, type) {
      $("button").css({ cursor: "pointer" });
      this.disabled = true;
      let _this = this;
      this.isActive = like;
      this.isActive = !this.isActive;
      if (this.isActive) {
        axios
          .post(
            apiRoute + "/user/" + type + "/" + id + "/likes",
            this.$store.getters["auth/token"]
          )
          .then(response => {
            this.liked = response.data.data;
            this.disabled = false;
            bus.$emit("like", 1);
          })
          .catch(error => {});
      } else {
        axios
          .delete(
            apiRoute + "/user/" + type + "/" + id + "/likes/" + likeId,
            this.$store.getters["auth/token"]
          )
          .then(response => {
            this.disabled = false;
            this.liked.likeableId = "deleteLiked";
            bus.$emit("like", 1);
          })
          .catch(error => {});
      }
    },

    removeCollectionable(collectionableId, collectionId, type) {
      bus.$emit("deleteFromCollection", {
        collectionableId: collectionableId,
        collectionId: collectionId,
        type: type
      });
    },

    hideProject(id, type) {
      let _this = this;
      axios
        .put(
          apiRoute + "/user/profiles/hide/update",
          { id: id, type: type },
          this.$store.getters["auth/token"]
        )
        .then(response => {
          this.collapsed = false;
          bus.$emit("refresh", { refresh: 1 });
        })
        .catch(error => {});
    },

    getUser() {
      let _this = this;
      axios
        .get(apiRoute + "/user", this.$store.getters["auth/token"])
        .then(response => {
          _this.user = response.data.data;
        })
        .catch(error => {});
    },

    removeProject(projectId, profileId) {
      this.projectId = projectId;
      this.profileId = profileId;
      this.type = "projects";
    }
  },

  created() {
    if (this.$route.name == "collection.detail") {
      this.collectionId = this.$router.history.current.params.id;
    }

    bus.$on("closeConfDialog", data => {
      if (data == 1) {
        this.showModal = false;
      }
    });

    this.getUser();
  }
};
</script>
