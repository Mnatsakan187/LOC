<template>
  <div class="discover">
    <!--WAVE filter starts-->
    <div class="wave-filter">
      <a href="javascript:void(0)" @click="active(1)">
        <div class="circle w" v-bind:class="{ selectedW: isActive1 }" @click="getWAVE(1)">W</div>
      </a>
      <a href="javascript:void(0)" @click="active(2)">
        <div class="circle a" v-bind:class="{ selectedA: isActive2 }" @click="getWAVE(2)">A</div>
      </a>
      <a href="javascript:void(0)" @click="active(3)">
        <div class="circle v" v-bind:class="{ selectedV: isActive3 }" @click="getWAVE(3)">V</div>
      </a>
      <a
        class="disabled"
        href="javascript:void(0)"
        v-tooltip.top="{
        content: 'Coming Soon',
        trigger: 'hover click'
      }"
      >
        <div class="circle e">E</div>
      </a>
    </div>
    <!--WAVE filter ends-->
    <!-- Tiles-->
    <TilesList v-bind:tilesList="tilesList"></TilesList>
    <div class="empty-content-discover" v-if="!tilesList.length  && !showLoad">
      <div class="line bolder">Nothing found!</div>
    </div>

    <div
      v-infinite-scroll="loadMore"
      infinite-scroll-disabled="busy"
      infinite-scroll-distance="10"
      infinite-scroll-throttle-delay="1500"
    ></div>
    <div class="loading" v-show="showLoad">
      <span class="fa fa-spinner fa-spin"></span>
    </div>

    <ModalAddToCollection
      v-if="showModal"
      :collectionableCollections="collectionableCollections"
      :type="type"
      :collectionableId="collectionableId"
    ></ModalAddToCollection>
  </div>
</template>

<script>
import TilesList from "../../shared/tiles/TilesList.vue";
import ModalAddToCollection from "../../shared/modals/ModalAddToCollection";
export default {
  name: "discover",
  middleware: "auth",
  components: {
    ModalAddToCollection,
    TilesList
  },
  data: function() {
    return {
      tilesList: [],
      perPage: 10,
      tooltipMsg: "Coming Soon",
      type: "",
      showLoad: true,
      busy: false,
      collectionableId: "",
      collectionableCollections: {},
      collections: {},
      collectionId: "",
      submitted: false,
      showModal: false,
      isActive1: false,
      isActive2: false,
      isActive3: false,
      lastTilesListLength: null,
      needShowLoader: false
    };
  },

  methods: {
    getHud(n) {
      if (n == undefined) {
        n = 1;
      }
      let _this = this;
      if (!this.showLoad && this.needShowLoader) this.showLoad = true;
      axios
        .get(
          apiRoute +
            "/user/discover?page=" +
            n +
            "&perPage=" +
            this.perPage +
            "&type=" +
            this.type,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.tilesList = [];
          if (response.data.data.length) {

            this.updateNeedShowLoader(response.data.data.length);

            response.data.data.forEach((value, key) => {
              let type;
              let image;
              let creativeTitle = "";
              let checkFollow = "";
              let wave = "";
              if (value.hudType == "project") {
                image = value.avatarUri;
                wave = value.type;
              } else if (value.hudType == "profile") {
                image = value.avatarUri;
                creativeTitle = value.creativeTitle;
                checkFollow = value.checkFollow;
              } else if (value.hudType == "collection") {
                image = value.backgroundUri;
              } else if (value.hudType == "event") {
                image = value.posterUri;
              }

              _this.tilesList.push({
                id: value.id,
                name: value.name,
                avatarUri: image,
                type: wave,
                likes: value.likes,
                userId: value.userId,
                creativeTitle: creativeTitle,
                profileId: value.profileId,
                collections: value.collections,
                user: {
                  firstName: value.user.firstName,
                  lastName: value.user.lastName
                },
                hudType: value.hudType,
                collectionBadge: true,
                color: value.updated,
                checkFollow: checkFollow,
                wave: wave,
                updatedColor: value.updatedColor
              });
            });
            this.busy = false;
            this.showLoad = false;
          } else {
            _this.lastTilesListLength = null;
            _this.tilesList = [];
            this.showLoad = false;
            this.busy = false;
          }
        })
        .catch(error => {
          this.showLoad = false;
          this.busy = false;
        });
    },

    loadMore: function() {
      if (!this.busy) {
        this.busy = true;
        this.perPage++;
        this.getHud();
      }

      if (this.tilesList.length < 11) {
        this.showLoad = true;
        this.busy = false;
      }
    },

    updateNeedShowLoader(dataLength) {
      if (!this.lastTilesListLength) {
        this.lastTilesListLength = dataLength;
        this.needShowLoader = true;
      } else {
        if (dataLength > this.lastTilesListLength) {
          this.lastTilesListLength = dataLength;
          this.needShowLoader = true;
        } else {
          this.needShowLoader = false;
        }
      }
    },

    active(type) {
      if (type == 1) {
        this.isActive2 = false;
        this.isActive3 = false;
        this.isActive1 = !this.isActive1;
      } else if (type == 2) {
        this.isActive3 = false;
        this.isActive1 = false;
        this.isActive2 = !this.isActive2;
      } else if (type == 3) {
        this.isActive2 = false;
        this.isActive1 = false;
        this.isActive3 = !this.isActive3;
      }
    },

    getWAVE(wave) {
      if (!this.isActive1 && wave == 1) {
        this.type = wave;
      } else if (!this.isActive2 && wave == 2) {
        this.type = wave;
      } else if (!this.isActive3 && wave == 3) {
        this.type = wave;
      } else {
        this.type = 0;
      }
      this.getHud(1);
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
    this.getHud(undefined);
    bus.$on("refresh", data => {
      if (data) {
        this.type = "";
        this.getHud(1);
      }
    });

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

    bus.$on("close", data => {
      if (data == 1) {
        this.showModal = false;
      }
    });
  },

  mounted() {
    this.getCollections();
  }
};
</script>

<style scoped lang="scss">
/* WAVE filter styles start */
.wave-filter {
  max-width: 300px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  a {
    text-decoration: none;
    &.disabled {
      cursor: default;
    }
    .circle {
      border-radius: 50%;
      width: 50px;
      height: 50px;
      font-weight: bold;
      font-size: 1.4rem;
      font-family: EncodeSansBold;
      display: flex;
      justify-content: center;
      align-items: center;
      &.w {
        color: #01ffc3;
        border: 1px solid #01ffc3;
      }
      &.a {
        color: #ff90fc;
        border: 1px solid #ff90fc;
      }
      &.v {
        color: #01aeff;
        border: 1px solid #01aeff;
      }
      &.e {
        color: #9d72ff;
        border: 1px solid #9d72ff;
      }
    }
  }
}

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
.selectedW {
  background-color: #01ffc3;
  color: #000 !important;
}

.selectedA {
  background-color: #ff90fc;
  color: #000 !important;
}

.selectedV {
  background-color: #01aeff;
  color: #000 !important;
}

.selectedE {
  background-color: #9d72ff;
  color: #000 !important;
}
/* WAVE filter styles end */
</style>
