<template>
  <div class="block poll-block mt-5">
    <div class="poll-header">
      <div class="type-date">
        <div class="date">{{dateChange(item.createdAt)}}</div>
        <div class="type">POLL</div>
      </div>
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
          <router-link class="dd-item" :to="{ path: '/polls/poll-detail/'+item.id}">
            View
          </router-link>
          <router-link v-if="user.id == item.userId"  class="dd-item" :to="{ path: '/polls/edit/'+item.id}">
            Edit
          </router-link>
          <a v-if="user.id == item.userId"  class="dd-item"  @click="removePoll(item.id, item.profileId)" v-on:click="showModal = true">
            Delete
          </a>

          <a class="dd-item"  @click="pinToTop(item.id, item.profileId)">
            Pin to top
          </a>
        </div>
      </div>
    </div>

    <div class="poll-name">{{item.name}}</div>
    <router-link :to="{ path: '/polls/poll-detail/'+item.id}" class="btn btn-respond">Respond</router-link>
    <div class="actions">
      <div class="likes-number">
        <i class="fas fa-heart isLike"></i> {{item.likeCount}}
      </div>

      <div class="action-buttons">

        <template  v-if="!liked.likeableId" >
          <button class="btn" :disabled="disabled" v-if="!item.likes" @click="like(item.id, false, 0, 'polls')"><i
            class="far fa-heart"></i>
          </button>
          <button class="btn" :disabled="disabled" v-else @click="like(item.id, true, item.likes.id, 'polls')"><i
            class="fas fa-heart isLike"></i>
          </button>
        </template>

        <template v-else >
          <button class="btn" :disabled="disabled" v-if="liked.likeableId != item.id" @click="like(item.id, false, 0, 'polls')"><i
            class="far fa-heart"></i>
          </button>
          <button class="btn" :disabled="disabled" v-else @click="like(item.id, true, liked.id, 'polls')"><i
            class="fas fa-heart isLike"></i>
          </button>
        </template>
      </div>
    </div>
    <ModalConfirmation v-if="showModal" :deleteId="pollId" :type="type" :profileId="profileId">
      <div slot="body">Are you sure, you want to delete this poll?</div>
    </ModalConfirmation>
  </div>
</template>

<script>
import ModalConfirmation from '../modals/ModalConfirmation'
export default {
  name: "PollBlock",
  components: { ModalConfirmation },
  props: ["item"],
  data() {
    return {
      collapsed: false,
      showModal:false,
      type: '',
      disabled: false,
      pollId: '',
      profileId:'',
      user:{},
      liked:{},

    };
  },
  methods: {
    toggleActionDropdown() {
      this.collapsed = !this.collapsed;
    },
    closeActionDropdown() {
      this.collapsed = false;
    },
    openModalConfirmation() {
      this.confirmData = {
        open: true,
        contentType: "poll"
      };
      this.$root.$emit("confirmation modal", this.confirmData);
    },

    like (id, like, likeId, type) {
      $('button').css({"cursor": "pointer"})
      this.disabled = true
      let _this = this
      this.isActive = like
      this.isActive = !this.isActive
      if (this.isActive) {
        axios.post(apiRoute + '/user/' + type + '/' + id + '/likes', this.$store.getters['auth/token']).then(response => {
          this.liked = response.data.data;
          this.disabled = false
          bus.$emit('like', 1)
        }).catch(error => {

        })
      } else {
        axios.delete(apiRoute + '/user/' + type + '/' + id + '/likes/' + likeId, this.$store.getters['auth/token']).then(response => {
          this.liked.likeableId = "deleteLiked"
          this.disabled = false
          bus.$emit('like', 1)
        }).catch(error => {

        })
      }

    },

    pinToTop(id, profileId){
      axios.post(apiRoute + '/user/polls/pin-to-top', {id: id, profileId: profileId}, this.$store.getters['auth/token']).then(response => {
        this.collapsed = false;
        bus.$emit('refresh', 1)
      }).catch(error => {

      })
    },

    removePoll(pollId, profileId){
      this.pollId = pollId;
      this.profileId = profileId;
      this.type  = 'polls';
    },

    dateChange: function (date) {
      return moment(date).format("MMM DD");
    },

    getUser() {
      let _this = this;
      axios.get(apiRoute + '/user',  this.$store.getters['auth/token']).then(response => {
        _this.user = response.data.data;
      }).catch(error => {

      });
    },
  },

  created(){
    bus.$on('closeConfDialog', (data) => {
      if(data == 1){
        this.showModal = false
      }
    })

    this.getUser();
  }
};
</script>

<style lang="scss">
.poll-block {
  border-bottom: 1px solid #333;
  padding-bottom: 20px;
  .poll-header {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    .type-date {
      font-size: 0.9rem;
    }
  }
  .poll-name {
    font-size: 1.3rem;
    margin: 10px 0;
    font-family: EncodeSansSemiBold;
  }
  .btn-respond {
    color: #fff;
    transition: 0.3s;
    border: 1px solid #fff;
    border-radius: 20px;
    padding: 10px 20px;
    &:hover {
      transition: 0.3s;
      color: #9d72ff;
      border: 1px solid #9d72ff;
    }
  }
  .actions {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    .btn {
      color: #fff;
      font-size: 1.2rem;
      transition: 0.3s;
      &:hover {
        color: #9d72ff;
        transition: 0.3s;
      }
    }
  }
}
</style>
