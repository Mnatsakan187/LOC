<template>
  <div class="my-favourite-collections">
    <div class="collection-blocks">
      <CollectionBlock
        v-for="item in collectionsList"
        v-bind:collection="item"
        v-bind:key="item.id"
      ></CollectionBlock>
    </div>
    <div class="empty-content-discover" v-if="!collectionsList.length && !showLoad">
      <div class="line bolder">There are no collections here!</div>

      <div class="line">Click a button below to start creating your first collection.</div>
      <button
        class="btn click-discover"
        v-on:click="$router.push('/collections/create')"
      >Create Collection</button>
    </div>

    <div class="loading" v-show="showLoad">
      <span class="fa fa-spinner fa-spin"></span>
    </div>
  </div>
</template>

<script>
import CollectionBlock from "../collections/CollectionBlock.vue";
export default {
  name: "MyFavouriteCollections",
  middleware: "auth",
  components: {
    CollectionBlock
  },
  data() {
    return {
      collapsed: false,
      collectionsList: [],
      showLoad: true,
      search: ""
    };
  },

  methods: {
    getCollections() {
      let _this = this;
      axios
        .get(
          apiRoute + "/user/profiles/1/collections",
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.collectionsList = response.data.data;
          _this.showLoad = false;
        })
        .catch(error => {
          _this.showLoad = false;
        });
    }
  },

  created() {
    bus.$on("refresh", data => {
      if (data) {
        this.getCollections();
      }
    });
  },

  mounted() {
    this.getCollections();
  }
};
</script>
<style scoped lang="scss">
.collection-blocks {
  display: flex;
  flex-flow: row wrap;

  @media (max-width: 550px) {
    display: block;
  }
}
</style>
