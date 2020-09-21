<template>
  <div class="block">
    <div class="title-dropdown">
      <span class="title">{{collection.name}}</span>
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
          <router-link
            class="dd-item"
            :to="{name: 'collection.detail', params: {id:collection.id, item: collection }}"
          >View</router-link>
          <router-link class="dd-item" :to="{ path: '/collection/edit/'+ collection.id}">Edit</router-link>
          <a
            class="dd-item"
            @click="showConfirmationModal = true"
            v-on:click="getCollectionId(collection.id)"
          >Delete</a>
        </div>
      </div>
    </div>

    <ModalConfirmation v-if="showConfirmationModal" :deleteId="deleteId" :type="type">
      <div slot="body">Unpack this Collection?</div>
    </ModalConfirmation>

    <div class="scroll-x-container">
      <div v-if="fourItems" class="collection-imgs">
        <template v-for="item in collection.collectionables">
          <img
            v-if="item.avatarUri && item.hudType=='project'"
            :src="'/storage/projects/projectAvatar/' + item.id + '/' + item.avatarUri"
          />
          <img
            v-if="item.avatarUri && item.hudType=='profile'"
            :src="'/storage/profiles/profilePictureImage/' + item.id + '/' + item.avatarUri"
          />
          <img
            v-if="item.avatarUri && item.hudType=='event'"
            :src="'/storage/events/poster/'+item.id+'/'+item.avatarUri"
          />
          <img v-if="!item.avatarUri && item.hudType=='project'" src="/images/project.png" />
          <img v-if="!item.avatarUri && item.hudType !='project'" src="/images/tiledefault.png" />
        </template>
      </div>
      <div v-if="moreThanFourItems" class="collection-imgs">
        <template v-for="item in collection.collectionables.slice(0,3)">
          <img
            v-if="item.avatarUri && item.hudType=='project'"
            :src="'/storage/projects/projectAvatar/' + item.id + '/' + item.avatarUri"
          />
          <img
            v-if="item.avatarUri && item.hudType=='profile'"
            :src="'/storage/profiles/profilePictureImage/' + item.id + '/' + item.avatarUri"
          />
          <img
            v-if="item.avatarUri && item.hudType=='event'"
            :src="'/storage/events/poster/'+item.id+'/'+item.avatarUri"
          />
          <img v-if="!item.avatarUri && item.hudType=='project'" src="/images/project.png" />
          <img v-if="!item.avatarUri && item.hudType !='project'" src="/images/tiledefault.png" />
        </template>

        <div class="left-items">
          <span>+ {{numberOfLeftItems}}</span>
        </div>
      </div>
      <div v-if="lessThanFourItems" class="collection-imgs">
        <template v-for="item in collection.collectionables.slice(0,3)">
          <img
            v-if="item.avatarUri && item.hudType=='project'"
            :src="'/storage/projects/projectAvatar/' + item.id + '/' + item.avatarUri"
          />
          <img
            v-if="item.avatarUri && item.hudType=='profile'"
            :src="'/storage/profiles/profilePictureImage/' + item.id + '/' + item.avatarUri"
          />
          <img
            v-if="item.avatarUri && item.hudType=='event'"
            :src="'/storage/events/poster/'+item.id+'/'+item.avatarUri"
          />
          <img v-if="!item.avatarUri && item.hudType=='project'" src="/images/project.png" />
          <img v-if="!item.avatarUri && item.hudType !='project'" src="/images/tiledefault.png" />
        </template>
        <div v-for="item in numberOfMissingItems" v-bind:key="item.id" class="missing-item"></div>
      </div>
      <div
        v-if="collection.collectionables.length==0"
        class="collection-empty d-flex justify-content-center align-items-center"
      >
        <div v-for="index in 4" :key="index" class="missing-item"></div>
        <span>This collection is empty</span>
      </div>
    </div>
  </div>
</template>

<script>
import ModalConfirmation from "../../shared/modals/ModalConfirmation";
export default {
  name: "CollectionBlock",
  middleware: "auth",
  components: { ModalConfirmation },
  props: ["collection"],
  data() {
    return {
      collapsed: false,
      fourItems: false,
      moreThanFourItems: false,
      numberOfLeftItems: 0,
      lessThanFourItems: false,
      numberOfMissingItems: 0,
      showConfirmationModal: false,
      type: "collection",
      deleteId: ""
    };
  },
  methods: {
    toggleActionDropdown() {
      this.collapsed = !this.collapsed;
    },
    closeActionDropdown() {
      this.collapsed = false;
    },

    getCollectionId(id) {
      this.deleteId = id;
    }
  },
  mounted() {
    if (this.collection.collectionables.length == 4) {
      this.fourItems = true;
    }
    if (this.collection.collectionables.length > 4) {
      this.moreThanFourItems = true;
      this.numberOfLeftItems = this.collection.collectionables.length - 3;
    }
    if (
      this.collection.collectionables.length < 4 &&
      this.collection.collectionables.length > 0
    ) {
      this.lessThanFourItems = true;
      this.numberOfMissingItems = 4 - this.collection.collectionables.length;
    }
  },

  created() {
    bus.$on("closeConfDialog", data => {
      if (data == 1) {
        this.showConfirmationModal = false;
      }
    });
  }
};
</script>
<style scoped lang="scss">
.block {
  width: 48%;
  background-color: #242424;
  margin-bottom: 20px;
  padding: 15px 25px;
  @media (max-width: 767px) {
    width: 100%;
  }

  &:nth-child(even) {
    margin-left: 20px;
    @media (max-width: 767px) {
      margin-left: 0;
    }
  }
  .title-dropdown {
    display: flex;
    justify-content: space-between;
    .title {
      color: #fff;
      font-family: EncodeSansSemiBold;
      font-size: 1.2rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .action-dropdown {
      position: relative;
      .dd-toggle {
        color: #fff;
        z-index: 1031;
        position: relative;
        &.show {
          color: #000;
        }
      }
      .action-dropdown-menu {
        display: none;
        position: absolute;
        top: -15px;
        right: -25px;
        z-index: 1030;
        background-color: rgba(255, 255, 255, 0.8);
        width: 10rem;
        padding-top: 25px;
        a {
          display: block;
          color: #000;
          text-decoration: none;
          border-bottom: 1px solid #000;
          padding: 15px;
          font-family: EncodeSansMedium;
          font-size: 0.8rem;
          transition: 0.3s;
          &:last-child {
            border-bottom: none;
          }
          &:hover {
            color: #333;
            background-color: #fff;
            transition: 0.3s;
          }
        }
        &.show {
          display: block;
        }
      }
    }
  }
  .scroll-x-container {
    @media (max-width: 500px) {
      width: 100%;
      overflow-y: scroll;
    }

    .collection-imgs {
      display: flex;

      @media (max-width: 500px) {
        min-width: 550px;
      }

      img {
        width: 23%;
        height: 100%;
        margin-right: 10px;
      }
      .left-items {
        width: 23%;
        text-align: center;
        background-color: #3f3f3f;
        color: #fff;
        font-family: EncodeSansRegular;
        font-size: 2rem;
        display: flex;
        justify-content: center;
        align-content: center;
        flex-direction: column;
      }
      .missing-item {
        width: 23%;
        background-color: #3f3f3f;
        margin-right: 10px;
      }
    }
    .collection-empty {
      position: relative;
      .missing-item {
        width: 23%;
        padding-top: 23%;
        background-color: #3f3f3f;
        margin-right: 10px;
      }
      span {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        text-align: center;
        font-size: 20px;
        color: white;
        margin-top: 30px;
        font-family: EncodeSansRegular;
        font-size: 1.2rem;
      }
    }
  }
}
</style>
