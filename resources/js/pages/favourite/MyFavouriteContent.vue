<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="loc-navigation">
          <router-link class="btn"  :to="{ name: 'my.fav.creators'}">Creators</router-link>
          <router-link class="btn" :to="{ name: 'my.fav.projects'}">Projects</router-link>
          <router-link class="btn" :to="{ name: 'my.fav.collections'}">Collections</router-link>
        </div>
        <router-view />
      </div>
    </div>
    <ModalAddToCollection v-if="showModal" :collectionableCollections="collectionableCollections" :type="type" :collectionableId="collectionableId"></ModalAddToCollection>
  </div>
</template>
<script>
import ModalAddToCollection from "../../shared/modals/ModalAddToCollection";
export default {
  name: "MyFavouriteContent",
  middleware: 'auth',
  components: {ModalAddToCollection},
  data: function() {
    return {
      collectionableId: '',
      collectionableCollections: {},
      collections: {},
      collectionId: '',
      submitted: false,
      type: '',
      showModal: false,
    };
  },

  methods: {
    getCollections(){
      let _this = this;
      axios.get(apiRoute + '/user/profiles/1/collections',   this.$store.getters['auth/token']).then(response => {
        this.collections = response.data.data;
      }).catch(error => {

      })
    },

    openCollectionModal(collectionableId, type, collections){
      this.type = type
      this.collectionableId = collectionableId;
      function comparer(otherArray){
        return function(current){
          return otherArray.filter(function(other){
            return other.id == current.id
          }).length == 0;
        }
      }
      var onlyInA =  collections.filter(comparer(this.collections));
      var onlyInB = this.collections.filter(comparer(collections));
      this.collectionableCollections  = onlyInA.concat(onlyInB);
      this.showModal = true;
    },

  },

  created() {
    this.showLoad = true;
    bus.$on('collection', (data) => {
      if(data){
        this.openCollectionModal(data.collectionableId, data.type, data.collections)
      }
    })

    bus.$on('close', (data) => {
      if(data == 1 ){
        this.showModal = false;
      }
    })
  },

  mounted() {
    this.getCollections();
  }
};
</script>

