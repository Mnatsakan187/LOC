<template>
  <div class="tile tile-event">
    <div class="info-dropdown d-flex justify-content-between">
      <div class="info">
        <span class="title">{{item.name}}</span>
        <span>By {{item.user.firstName + ' '+ item.user.lastName }}</span>
        <span>{{item.startDate}}</span>
        <span>{{item.venue}}</span>
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
          <a class="dd-item" @click="hideEvent(item.id, 'event')">Hide from HUD</a>
          <a
            v-if="routeName == 'my.feed'"
            class="dd-item"
            @click="openCollectionModal(item.id, 'events', item.collections)"
          >Add to collection</a>
          <a class="dd-item" v-if="routeName=='collection.detail'">Remove from collection</a>
        </div>
      </div>
    </div>

    <img
      v-if="item.avatarUri"
      :class="{'updated': item.updatedColor}"
      :src="'/storage/events/poster/'+item.id+'/'+item.avatarUri" class="tile-image"
    />
    <img v-else :class="{'updated': item.updatedColor}" src="/images/tiledefault.png" class="tile-image" />
    <div v-if="item.updatedColor" class="overlay e"></div>

    <div class="follow-action-buttons d-flex justify-content-between align-items-center">
      <div class="follow-statistics">
        <span class="type-circle">e</span>
      </div>
      <div class="action-area">
        <button :disabled="disabled" v-if="!item.likes" @click="like(item.id, false, 0, 'events')">
          <i class="far fa-heart"></i>
        </button>
        <button :disabled="disabled" v-else @click="like(item.id, true, item.likes.id, 'events')">
          <i class="fas fa-heart isLike"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TileEvent",
  props: ["item"],
  data() {
    return {
      collapsed: false,
      disabled: false,
      isLike: false
    };
  },

  computed: {
    routeName() {
      return this.$route.name;
    }
  },

  methods: {
    toggleActionDropdown() {
      this.collapsed = !this.collapsed;
    },
    closeActionDropdown() {
      this.collapsed = false;
    },
    like(id, like, likeId, type) {
      $('button').css({"cursor": "pointer"})
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
            bus.$emit("like", 1);
          })
          .catch(error => {});
      }
    },

    openCollectionModal(collectionableId, type, collections) {
      this.collapsed = false;
      bus.$emit("collection", {
        collectionableId: collectionableId,
        type: type,
        collections: collections
      });
    },

    hideEvent(id, type) {
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
    }
  },
  created() {},

  mounted() {}
};
</script>
