<template>
    <div class="group-list">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 mt-5">
            <ul class="list">
              <GroupItem v-for="group in groupList" v-bind:key="group.id" v-bind:group="group"></GroupItem>
            </ul>
            <ModalConfirmation  v-if="showConfModal" :deleteId="deleteId" :type="type" @close="showConfModal = false">
              <div slot="body">Are you sure, you want to delete this group?</div>
            </ModalConfirmation>
          </div>
        </div>
      </div>

      <div class="empty-content-discover"  v-if="groupEmptyShow">
        <div class="line bolder">You don't have any groups yet!</div>

        <div class="line">Click a button below to start creating your first group.</div>
        <button class="btn click-discover" v-on:click="$router.push('/groups/create')">Create Group</button>
      </div>
      <div v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-distance="10"></div>
      <div class="loading" v-show="showLoad">
        <span class="fa fa-spinner fa-spin"></span>
      </div>
    </div>
</template>
<script>
import GroupItem from "../../shared/groupItem/GroupItem.vue";
import ModalConfirmation from "../../shared/modals/ModalConfirmation.vue";
export default {
  name: "GroupList",
  middleware: 'auth',
  components: {
    GroupItem,
    ModalConfirmation
  },
  middleware: 'auth',
  data: function() {
    return {
      groupList: [],
      collapsed: null,
      openDropdown: null,
      showConfModal: false,
      groups: {},
      pagination: {
        currentPage: 1,
        total: '',
      },
      deleteId: '',
      type: 'group',
      groupEmptyShow: false,
      showLoad:false,
      perPage:10,
    };
  },
  methods: {
    paginateGroups() {
      this.showLoad = true;
      axios.get(apiRoute + '/user/groups?page=1&perPage='+this.perPage,  this.$store.getters['auth/token']).then(response => {
        let _this = this;
        this.showLoad = false
        if(!response.data.data.length){
          this.groupEmptyShow = true
        }
        this.groupList = response.data.data;
        _this.pagination.total = response.data.meta.last_page;

      }).catch(error => {
        this.showLoad = false
      })
    },


    loadMore: function() {
      this.busy = true;
      this.showLoader = true;
      this.perPage++;
      this.paginateGroups()
    },

  },

  created(){

    bus.$on('group', (data) => {
      if(data){
        this.deleteId = data.id
      }
    })

    bus.$on('refresh', (data) => {
      this.paginateGroups();
      this.showConfModal = false;
    })
  },

  mounted() {
    this.$root.$on("confirmation modal", openModal => {
      this.showConfModal = openModal;
    });

    this.paginateGroups()

  }
};
</script>
<style lang="scss">
.group-list {
  padding-bottom: 50px;
  @media (max-width: 600px) {
    padding: 0 20px;
  }
  .list {
    margin: 0;
    padding: 0;
    list-style: none;
    li {
      padding: 20px 0;
      border-bottom: 1px solid #333;
      .group-name {
        font-size: 1.2rem;
      }
      .name-dropdown {
        display: flex;
        justify-content: space-between;
      }
      .members {
        display: flex;
        justify-content: flex-start;
        img {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          margin-right: 10px;
        }
        .more-members {
          color: #fff;
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background-color: #333;
          display: flex;
          justify-content: center;
          align-content: center;
          flex-direction: column;
          text-align: center;
        }
      }
      .no-members {
        height: 40px;
        font-size: 0.9rem;
      }
      a {
        text-decoration: none;
        color: #fff;
        transition: 0.3s;
        &:hover {
          color: #9d72ff;
          transition: 0.3s;
        }
      }
    }
  }
  .action-dropdown {
    position: relative;
    .dd-toggle {
      color: #fff;
      z-index: 1001;
      position: relative;
      &.show {
        color: #000;
      }
    }
    .action-dropdown-menu {
      display: none;
      position: absolute;
      top: 0;
      right: 0;
      z-index: 1000;
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
</style>
