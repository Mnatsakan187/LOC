<template>
  <div class="tile tile-profile">
    <div class="info-dropdown d-flex justify-content-between">
      <div class="info"  @click="openUrl(item.id)">
        <span class="title">{{item.user.firstName + ' '+ item.user.lastName }}</span>
        <span>{{item.creativeTitle}}</span>
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
        <div  class="action-dropdown-menu" :class="{'show': collapsed}">
          <a class="dd-item" v-if="routeName == 'my.feed' || routeName == 'discover'"  @click="hideProfile(item.id, 'profile')">Hide from HUD</a>
          <a v-if="routeName == 'my.feed'" class="dd-item" @click="stopFollowing(item.id)">Stop following</a>
          <a v-if="routeName == 'my.feed' || routeName == 'my.fav.creators' || routeName == 'discover'" class="dd-item"  @click="openCollectionModal(item.id, 'profiles', item.collections)">Add to collection</a>
          <a class="dd-item" v-if="routeName=='collection.detail'"  @click="removeCollectionable(item.id, collectionId, 'projects')" >Remove from collection</a>
          <a v-if="routeName == 'discover' && item.checkFollow == 0" class="dd-item" @click="following(item.id)">
            Follow this profile
          </a>
          <a v-if="(routeName == 'discover' && item.checkFollow == 1) || (routeName == 'my.fav.creators' && item.checkFollow == 1)" class="dd-item" @click="stopFollowing(item.id)">
            Stop following
          </a>
        </div>
      </div>
    </div>

    <img v-if="item.avatarUri" :class="{'updated': item.updatedColor && item.color}" :src="'/storage/profiles/profilePictureImage/' + item.id + '/' + item.avatarUri" class="tile-image" />
    <img v-else :class="{'updated': item.updatedColor && item.color}" src="/images/tiledefault.png" class="tile-image" />
    <div class="overlay" v-if="item.updatedColor && item.color" :class="getWAVEtype(item.type)"></div>

    <div class="follow-action-buttons d-flex justify-content-start align-items-center">
      <div class="follow-statistics">
        <span class="type-circle">{{getWAVEtype(item.type)}}</span>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "TileProfile",
    props: ["item"],
    data() {
      return {
        collapsed: false,
        collectionId: '',
      };
    },

    computed: {
      routeName () {
        return this.$route.name
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

      openUrl(id){
        this.$router.push({ path: '/profile-detail/'+id})
      },

      toggleActionDropdown() {
        this.collapsed = !this.collapsed;
      },
      closeActionDropdown() {
        this.collapsed = false;
      },

      openCollectionModal(collectionableId, type, collections){
        this.collapsed = false;
        bus.$emit('collection', {collectionableId:collectionableId, type:type, collections:collections})
      },

      hideProfile(id, type){
        axios.put(apiRoute + '/user/profiles/hide/update', {id:id, type:type},   this.$store.getters['auth/token']).then(response => {
          this.collapsed = false;
          bus.$emit('refresh', {refresh:1})
        }).catch(error => {

        })
      },

      stopFollowing(id){
        axios.delete(apiRoute + '/user/follows/'+id, this.$store.getters['auth/token']).then(response => {
          this.collapsed = false;
          bus.$emit('refresh', {refresh:1})
        }).catch(error => {

        })
      },

      following(id){
        axios.post(apiRoute + '/user/follows/'+id, this.$store.getters['auth/token']).then(response => {
          this.collapsed = false;
          bus.$emit('refresh', {refresh:1})
        }).catch(error => {

        })
      },

      removeCollectionable(collectionableId, collectionId, type){
        bus.$emit('deleteFromCollection', {collectionableId:collectionableId, collectionId:collectionId, type:type })
      },

    },

    created(){
      if (this.$route.name == 'collection.detail'){
        this.collectionId = this.$router.history.current.params.id;
      }

    }
  };
</script>
