<template>
  <div class="block project-block mt-5">
    <div class="name-dropdown">
      <div class="date" :class="getWAVEtype(item.type)">{{dateChange(item.createdAt)}}</div>

      <div class="news-action-dropdown" v-click-outside="closeActionDropdown">
        <button
          class="btn dd-toggle"
          :class="{'show': collapsed}"
          v-on:click="toggleActionDropdown"
          type="button"
        >
          <i class="fas fa-ellipsis-h"></i>
        </button>
        <div class="action-dropdown-menu" :class="{'show': collapsed}">
          <router-link class="dd-item" v-if="item.userId== item.userId"  :to="{ path: '/project-media/'+item.projectId+'/project/edit/'+item.id}">
            Edit
          </router-link>

          <a class="dd-item" v-if="item.userId== item.userId"  v-on:click="showModal = true" @click="removeLink(item.id, item.projectId)">
            Delete
          </a>
        </div>
      </div>
    </div>
    <div class="type" :class="getWAVEtype(item.type)">LINK</div>
    <div class="project-name" :class="getWAVEtype(item.type)">{{item.name}}</div>
    <div class="project-image mt-3">
      <div v-if="item.uri.includes('youtube')"   class="project-detail-link mt-3">
        <p>{{item.displayName}}</p>
        <iframe class="iframe-project"  height="450" width="650" :src="item.uri.replace('watch?v=', 'embed/')" frameborder="0" allowfullscreen></iframe>
      </div>

      <div  v-if="item.uri.includes('instagram')"    class="project-detail-link mt-3">
        <p>{{item.displayName}}</p>
        <iframe class="iframe-project"  height="450"  width="650" :src="item.uri.replace('?utm_source=ig_web_copy_link', 'embed')" frameborder="0" allowfullscreen></iframe>
      </div>

      <div v-if="!item.uri.includes('youtube') && !item.uri.includes('instagram')"     class="project-detail-text mt-3">
        <a class="project-link" :href="item.uri">Project Link - {{item.uri}}</a>
      </div>
    </div>
    <div class="project-type mt-3" :class="getWAVEtype(item.type)">{{getWAVEtype(item.type)}}</div>
    <div class="project-text mt-3">
      {{item.description}}
    </div>

    <ModalConfirmation v-if="showModal" :deleteId="deleteId" :type="type" :projectId="projectId">
      <div slot="body">Are you sure, you want to delete this project?</div>
    </ModalConfirmation>
  </div>
</template>

<script>
import ModalConfirmation from '../modals/ModalConfirmation'
export default {
  name: "LinkBlock",
  components: { ModalConfirmation },
  props: ["item"],
  data() {
    return {
      collapsed: false,
      showModal: false,
      type: '',
      disabled: false,
      projectId: '',
      profileId:'',
      facebookShareUrl: window.config.facebookShareUrl,
      deleteId: ''
    };
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
    openModalConfirmation() {
      this.confirmData = {
        open: true,
        contentType: "project"
      };
      this.$root.$emit("confirmation modal", this.confirmData);
    },
    toggleActionDropdown() {
      this.collapsed = !this.collapsed;
    },
    closeActionDropdown() {
      this.collapsed = false;
    },

    removeLink(id, projectId){
      this.deleteId = id;
      this.projectId = projectId;
      this.type  = 'media';
    },

    like (id, like, likeId, type) {
      this.disabled = true
      let _this = this
      this.isActive = like
      this.isActive = !this.isActive
      if (this.isActive) {
        axios.post(apiRoute + '/user/' + type + '/' + id + '/likes', this.$store.getters['auth/token']).then(response => {
          this.disabled = false
          bus.$emit('like', 1)
        }).catch(error => {

        })
      } else {
        axios.delete(apiRoute + '/user/' + type + '/' + id + '/likes/' + likeId, this.$store.getters['auth/token']).then(response => {
          this.disabled = false
          bus.$emit('like', 1)
        }).catch(error => {

        })
      }

    },

    close(type, id, shareCount){
      let _this = this;
      if(type == 'projects') {
        axios.post(apiRoute + '/user/'+type+'/'+id+'/share', {'shareCount': shareCount}, this.$store.getters['auth/token']).then(response => {
          _this.profileView();
        }).catch(error => {

        })
      }else {
        axios.post(apiRoute + '/user/'+type+'/'+id+'/share', {'shareCount': shareCount}, this.$store.getters['auth/token']).then(response => {
          _this.profileView();
        }).catch(error => {

        })
      }
    },

    dateChange: function (date) {
      return moment(date).format("MMM DD");
    },

  },

  created(){
    bus.$on('closeConfDialog', (data) => {
      if(data == 1){
        this.showModal = false
      }
    })
  }
};
</script>

<style lang="scss">
.project-block {
  border-bottom: 1px solid #333;
  padding-bottom: 20px;
  .name-dropdown {
    display: flex;
    justify-content: space-between;
  }
  .project-name {
    font-size: 1.3rem;
    margin-bottom: 5px;
    font-family: EncodeSansSemiBold;
    &.w {
      color: #01ffc3;
    }
    &.a {
      color: #ff90fc;
    }
    &.v {
      color: #01aeff;
    }
  }
  .type,
  .date {
    text-transform: uppercase;
    font-size: 0.9rem;
    &.w {
      color: #01ffc3;
    }
    &.a {
      color: #ff90fc;
    }
    &.v {
      color: #01aeff;
    }
  }
  .project-type {
    &.w {
      color: #01ffc3;
    }
    &.a {
      color: #ff90fc;
    }
    &.v {
      color: #01aeff;
    }
  }
  .project-image {
    img {
      max-width: 100%;
      max-height: 400px;
    }
  }
  .project-text {
    font-size: 1rem;
    text-align: justify;
  }
  .actions {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    &.w {
      color: white;
    }
    &.a {
      color: white;
    }
    &.v {
      color: white;
    }
    .btn {
      color: inherit;
      font-size: 1.2rem;
      opacity: 1;
      transition: 0.3s;
      &:hover {
        opacity: 0.6;
        transition: 0.3s;
      }
    }
  }
}
</style>
