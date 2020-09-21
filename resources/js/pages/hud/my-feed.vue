<template>
  <div class="myfeed">
    <!-- Tiles-->
    <TilesList v-bind:tilesList="tilesList"></TilesList>
    <div class="empty-content-discover" v-if="show">
      <div class="line bolder">There are no updates!</div>

      <div class="line">Click discover to search for more new content.</div>
      <button class="btn click-discover" v-on:click="$router.push('discover')">Discover</button>
    </div>
    <ModalAddToCollection
      v-if="showModal"
      :collectionableCollections="collectionableCollections"
      :type="type"
      :collectionableId="collectionableId"
    ></ModalAddToCollection>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-distance="10"></div>
    <div class="loading" v-show="showLoad">
      <span class="fa fa-spinner fa-spin"></span>
    </div>
  </div>
</template>

<script>
import TilesList from "../../shared/tiles/TilesList";
import ModalAddToCollection from "../../shared/modals/ModalAddToCollection";

export default {
  name: "MyFeed",
  middleware: "auth",
  components: {
    ModalAddToCollection,
    TilesList
  },
  data: function() {
    return {
      tilesList: [],
      perPage: 10,
      type: "",
      collectionableId: "",
      collectionableCollections: [],
      collections: {},
      collectionId: "",
      submitted: false,
      list: [],
      showLoad: false,
      busy: false,
      show: false,
      showModal: false
    };
  },

  methods: {
    getHud(n) {
      if (n == undefined) {
        n = 1;
      }
      let _this = this;
      this.showLoad = true;
      axios
        .get(
          apiRoute + "/user/hud?perPage=" + this.perPage,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.tilesList = [];

          if (response.data.data.length == 0) {
            this.show = true;
          }
          response.data.data.forEach((value, key) => {
            let type;
            let image;
            let creativeTitle = "";
            let startDate = "";
            let venue = "";
            let collectionBadge = false;
            let allValue = "";
            let wave = "";
            if (value.hudType == "project") {
              type = 0;
              image = value.avatarUri;
              wave = value.type;
            } else if (value.hudType == "profile") {
              type = 1;
              image = value.avatarUri;
              creativeTitle = value.creativeTitle;
            } else if (value.hudType == "collection") {
              if (value.collectionables.length) {
                value.collectionables.forEach((item, key) => {
                  if (item.updated == 1) {
                    collectionBadge = true;
                  }
                });
              } else {
                collectionBadge = false;
              }

              type = 4;
              image = value.backgroundUri;
              allValue = value;
            } else if (value.hudType == "event") {
              type = 3;
              image = value.posterUri;
              startDate = value.startDate;
              venue = value.venue;
            }

            _this.tilesList.push({
              id: value.id,
              name: value.name,
              userId: value.userId,
              creativeTitle: creativeTitle,
              avatarUri: image,
              likes: value.likes,
              profileId: value.profileId,
              collections: value.collections,
              type: wave,
              startDate: startDate,
              venue: venue,
              user: {
                firstName: value.user.firstName,
                lastName: value.user.lastName
              },
              hudType: value.hudType,
              color: value.updated,
              collectionBadge: collectionBadge,
              checkFollow: 1,
              wave: wave,
              allValue: allValue,
              updatedColor: value.updatedColor
            });
          });

          this.busy = false;
          this.showLoad = false;
        })
        .catch(error => {
          this.showLoad = false;
          this.busy = false;
        });
    },

    loadMore: function() {
      if (!this.busy) {
        this.busy = true;
        this.showLoad = true;
        this.perPage++;
        this.getHud();
      }

      if (this.tilesList.length < 11) {
        this.showLoad = true;
        this.busy = false;
      }
    },

    getCollections() {
      let _this = this;
      axios
        .get(
          apiRoute + "/user/profiles/1/collections",
          this.$store.getters["auth/token"]
        )
        .then(response => {
          this.collections = response.data.data;
        })
        .catch(error => {});
    },

    openCollectionModal(collectionableId, type, collections) {
      this.type = type;
      this.collectionableId = collectionableId;
      function comparer(otherArray) {
        return function(current) {
          return (
            otherArray.filter(function(other) {
              return other.id == current.id;
            }).length == 0
          );
        };
      }
      var onlyInA = collections.filter(comparer(this.collections));
      var onlyInB = this.collections.filter(comparer(collections));
      this.collectionableCollections = onlyInA.concat(onlyInB);
      this.showModal = true;
    }
  },

  created() {
    this.showLoad = true;
    bus.$on("like", data => {
      if (data == 1) {
        this.getHud(1);
      }
    });

    bus.$on("collection", data => {
      if (data) {
        this.openCollectionModal(
          data.collectionableId,
          data.type,
          data.collections
        );
      }
    });

    bus.$on("refresh", data => {
      if (data) {
        this.getHud(1);
      }
    });

    bus.$on("close", data => {
      if (data == 1) {
        this.showModal = false;
      }
    });
  },

  mounted() {
    this.showLoad = true;
    this.getCollections();
  }
};
</script>

<style>
.loading {
  text-align: center;
  position: fixed;
  color: #fff;
  z-index: 9;
  padding: 8px 18px;
  border-radius: 5px;
  left: calc(50% - 45px);
  top: calc(50% - 18px);
  font-size: 40px;
}
</style>

