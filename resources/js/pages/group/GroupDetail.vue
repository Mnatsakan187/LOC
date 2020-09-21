<template>
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-8 offset-md-2 mt-5">
          <div class="group-detail-top">
            <div class="title">{{group.name}}</div>
            <div class="bio" v-html="profileDescription"></div>
            <div class="members">
              <div v-for="member in members.slice(0,3)">
                <img  v-if="member.avatarUri" :src="'/storage/avatarImage/' + member.id + '/' + member.avatarUri"/>
                <img v-else  src="/images/user8-128x128.png"/>
              </div>

              <router-link v-if="members.length>0" class="link" :to="{ path: '/groups/'+group.id+'/members'}">
                See all
                <i class="fas fa-arrow-right"></i>
              </router-link>
            </div>
            <div  class="no-members" v-if="noMember">
              No members:

              <router-link class="add-members"  :to="{ path: '/groups/'+group.id+'/members'}">Add members</router-link>
            </div>
          </div>
          <NewsFeed v-bind:newsList="newsList"></NewsFeed>
          <ModalConfirmation v-if="showConfModal" @close="showConfModal = false">
            <div slot="body">Are you sure, you want to delete this {{typeofContent}}?</div>
          </ModalConfirmation>
          <div class="group-empty-content mt-5" v-if="groupFeed">
            <div class="line bolder">Group feed is empty.</div>
            <div class="line">Create your first post!</div>
            <button class="btn" v-on:click="$router.push('/posts/create')">Add post</button>
          </div>
        </div>
      </div>

      <div class="loading" v-show="showLoad">
        <span class="fa fa-spinner fa-spin"></span>
      </div>
    </div>
</template>
<script>
  import ModalConfirmation from "../../shared/modals/ModalConfirmation.vue";
  import NewsFeed from "../../shared/newsfeed/NewsFeed.vue";
  export default {
    name: "GroupDetail",
    middleware: 'auth',
    components: {
      NewsFeed,
      ModalConfirmation
    },
    data: function() {
      return {
        groupMembers: [],
        newsList: [],
        typeofContent: "",
        showConfModal: false,
        showAddGroupMembers: false,
        group: {},
        members: [],
        groupFeed: false,
        noMember:false,
        profileDescription: '',
        showLoad: false,
      };
    },
    methods:{
      getGroup () {
        this.showLoad = true;
        let _this = this
        axios.get(apiRoute + '/user/groups/' + this.groupId, this.$store.getters['auth/token']).then(response => {

          if(!response.data.data.posts.length){
            this.groupFeed = true;
          }

          if(response.data.data.description.includes('href=')){
            let projectSum = response.data.data.description.replace('href="', 'target="_blank" href=');
            this.profileDescription = projectSum.replace(/["']/g, "")
          }else{
            this.profileDescription= response.data.data.description;
          }

          if(!response.data.data.members.length){
            this.noMember = true;
          }

          _this.group = response.data.data
          _this.members = response.data.data.members
          _this.newsList = response.data.data.posts
          this.showLoad = false;
        }).catch(error => {

        })
      },
    },

    created () {
      this.groupId = this.$route.params.id

      bus.$on('like', (data) => {
        if(data == 1 ){
          this.getGroup()
        }
      })
    },

    mounted() {
      this.getGroup();

      this.$root.$on("confirmation modal", openModal => {
        this.showConfModal = openModal.open;
        this.typeofContent = openModal.contentType;
      });

      this.newsList = [
        {
          id: 1,
          newsType: "post"
        },
        {
          id: 2,
          newsType: "post"
        },
        {
          id: 3,
          newsType: "post"
        }
      ];
      this.groupMembers = [
        {
          id: 1,
          name: "Group Member"
        },
        {
          id: 2,
          name: "Group Member"
        },
        {
          id: 3,
          name: "Group Member"
        },
        {
          id: 4,
          name: "Group Member"
        },
        {
          id: 5,
          name: "Group Member"
        },
        {
          id: 6,
          name: "Group Member"
        },
        {
          id: 7,
          name: "Group Member"
        },
        {
          id: 8,
          name: "Group Member"
        },
        {
          id: 9,
          name: "Group Member"
        },
        {
          id: 10,
          name: "Group Member"
        }
      ];
    }
  };
</script>
<style scoped lang="scss">
  .group-empty-content {
    text-align: center;
    .line {
      padding-bottom: 20px;
      color: #fff;
      font-family: EncodeSansRegular;
      font-size: 1.2rem;
      &.bolder {
        font-family: EncodeSansSemiBold;
        font-size: 1.6rem;
      }
    }
    .btn {
      margin: 0 auto;
      display: block;
      color: #fff;
      border: 1px solid #fff;
      border-radius: 24px;
      font-size: 1.2rem;
      transition: 0.5s;
      margin-bottom: 20px;
      padding: 10px 20px;
      &:hover {
        color: #9d72ff;
        border: 1px solid #9d72ff;
        transition: 0.5s;
      }
    }
  }
  .group-detail-top {
    @media (max-width: 600px) {
      padding-left: 20px;
      padding-right: 20px;
    }
    padding-bottom: 40px;
    border-bottom: 4px solid #333;
    .title {
      font-size: 2rem;
    }
    .bio {
      margin-top: 20px;
      text-align: justify;
      font-size: 1.2rem;
    }
    .members {
      margin-top: 20px;
      display: flex;
      justify-content: flex-start;
      align-items: center;

      img {
        width: 50px;
        height: auto;
        border-radius: 50%;
        margin-right: 20px;
      }
    }
    .no-members {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      margin-top: 20px;
      .add-members {
        display: block;
        text-align: center;
        width: 150px;
        color: #fff;
        margin-left: 20px;
        border: 1px solid #fff;
        transition: 0.3s;
        border-radius: 20px;
        padding: 15px 20px;
        text-decoration: none;
        &:hover {
          color: #9d72ff;
          border: 1px solid #9d72ff;
          transition: 0.3s;
        }
      }
    }
    .link {
      text-decoration: none;
      color: #fff;
      font-size: 1rem;
      transition: 0.3s;
      &:hover {
        color: #9d72ff;
        transition: 0.3s;
      }
    }
  }
</style>
