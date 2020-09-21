<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="collections-detail mt-3">
          <span class="title">{{item.name}}</span>
          <TilesList v-bind:tilesList="item.collectionables"></TilesList>
        </div>
        <div class="empty-content-discover" v-if="!item.collectionables.length">
          <div class="line bolder">This collection is empty</div>

          <div class="line">Click "Discover" to search for content to add.</div>
          <button class="btn click-discover" v-on:click="routerPushToDiscover">Discover</button>
        </div>
      </div>
    </div>

    <ModalConfirmation :type="type" :itemType="itemType" :collectionableId="collectionableId" :collectionId="collectionId" v-if="showConfirmationModalItem">
      <div slot="body">Unpack this Collection item?</div>
    </ModalConfirmation>
  </div>
</template>

<script>
import TilesList from "../../shared/tiles/TilesList.vue";
import ModalConfirmation from "../../shared/modals/ModalConfirmation";
export default {
  name: "CollectionDetail",
  middleware: 'auth',
  props: { item: Object },
  components: {
    ModalConfirmation,
    TilesList
  },

  data() {
    return {
      collapsed: false,
      collectionableId: '',
      collectionId: '',
      type: '',
      collection: {},
      showConfirmationModalItem:false,
      itemType: ''

    };
  },
  methods: {
    deleteCollectionable(){
      let _this = this;
      axios.delete(apiRoute+ '/user/collections/'+this.collectionId+'/'+this.type+'/'+ this.collectionableId).then(response => {
        $('#modal-remove-favourite-collectionable').modal('hide');
        _this.getCollection();
      }).catch(function (error) {

      });
    },

    getCollection(){
      let _this = this;
      axios.get(apiRoute + '/user/profiles/1/collections/'+this.collectionId,   this.$store.getters['auth/token']).then(response => {
        _this.item.collectionables = response.data.data.collectionables
      }).catch(error => {

      })
    },

    routerPushToDiscover(){
      this.$router.push({ path: '/discover'})
    }
  },

  created() {
    this.showLoad = true;
    bus.$on('deleteFromCollection', (data) => {
      this.showConfirmationModalItem = true
      this.collectionableId = data.collectionableId;
      this.collectionId     = data.collectionId;
      this.itemType = data.type;
      this.type = 'item';
    }),

    bus.$on('closeConfDialog', (data) => {
      if(data == 1){
        this.showConfirmationModalItem = false;
        this.getCollection();
      }
    })

  },

  mounted() {

  }
};
</script>
<style scoped lang="scss">
.title {
  color: #fff;
  font-family: EncodeSansSemiBold;
  font-size: 1.2rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-left: 15px;
}

@media (max-width: 500px) {
   .line{
     padding-left: 10px;
     padding-right: 10px;
   }
}
</style>
