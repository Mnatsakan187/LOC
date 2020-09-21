<template>
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3 mt-3">
        <div class="account">
          <div class="top d-flex justify-content-between align-items-center">
            <div class="title">My account</div>
            <router-link class="profiles-action-button" :to="{ name: 'choose.plan' }">
              <i class="far fa-credit-card"></i> Upgrade
            </router-link>
          </div>
          <table class="table mt-4">
            <thead class="sr-only">
            <tr>
              <th scope="col">Account</th>
              <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <template v-if="user">
              <tr>
                <th scope="row">
                  <a href="#" class="icon-name disabled">
                    <img v-if="!user.avatarUri"
                      src="/images/user8-128x128.png"
                      alt="default"
                      class="profile-img"
                    />
                    <img v-else="user.avatarUri"
                         :src="'/storage/avatarImage/' + user.id + '/' + user.avatarUri"
                         alt="default"
                         class="profile-img"
                    />
                    <span>{{user.firstName}}  {{user.lastName}}</span>
                  </a>
                </th>
                <td class="d-flex justify-content-end align-items-center">
                  <router-link class="btn" to="/settings">Edit</router-link>|
                  <a href="#" class="btn">Delete</a>
                </td>
              </tr>
            </template>

            </tbody>
          </table>
        </div>
        <div class="profiles mt-5">
          <div class="top d-flex justify-content-between align-items-center">
            <div class="title">My profiles</div>
            <div v-if="user.accountType == 1">
              <ModalPayment v-if="sub == 0 && showPaymentModal == true">
                <div slot="body">{{messages}}</div>
              </ModalPayment>
              <button v-if="sub == 0"  class="profiles-action-button" data-toggle="modal" data-target="#subscriptionModal" @click="showPaymentModal = true">
                <i class="fas fa-plus"></i> Add New
              </button>
              <router-link v-else class="profiles-action-button" :to="{ name: 'profile.create' }">
                <i class="fas fa-plus"></i> Add New
              </router-link>
            </div>
          </div>
          <table class="table mt-4">
            <thead class="sr-only">
            <tr>
              <th scope="col">Account</th>
              <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="profile in profiles">
              <tr>
                <th scope="row">
                  <router-link class="icon-name" :to="{ path: '/profile-detail/'+profile.id}">
                    <img v-if="profile.avatarUri" :src="'/storage/profiles/profilePictureImage/' + profile.id + '/' + profile.avatarUri" alt="default" class="profile-img" />
                    <img v-else src="/images/user8-128x128.png" alt="default" class="profile-img" />
                    <span>{{profile.creativeTitle}}</span>
                  </router-link>
                </th>
                <td class="d-flex justify-content-end align-items-center">
                  <router-link class="btn" :to="{ path: '/profiles/edit/'+profile.id}">Edit</router-link>|
                  <a href="#" class="btn"  @click="openConfModal('profile', profile.id)" >Delete</a>
                </td>
              </tr>
            </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <ModalConfirmation :deleteId="deleteId" :type="typeofAccount" v-if="showConfModal" @close="showConfModal = false">
      <div slot="body">Are you sure, you want to delete this {{typeofAccount}}?</div>
    </ModalConfirmation>
  </div>
</template>

<script>
  import Modal from "../../shared/modals/profile-modal";
  import ModalConfirmation from "../../shared/modals/ModalConfirmation";
  import ModalPayment from "../../shared/modals/ModalPayment";
  export default {
    name: "index",
    components: {ModalPayment, ModalConfirmation, Modal},
    middleware: 'auth',
    data() {
      return {
        profiles: {},
        pagination: {
          currentPage: 1,
          total: '',
        },
        user: {},
        sub: 0,
        profilesTotal: '',
        messages: '',
        showConfModal: false,
        deleteId: '',
        typeofAccount: '',
        showPaymentModal: false,
      }
    },

    methods: {
      paginateProfile(n) {
        this.pagination.currentPage = n;
        axios.get(apiRoute + '/user/profiles?page=' + n, this.$store.getters['auth/token']).then(response => {
          let _this = this;
          _this.profiles = response.data.data;
          _this.pagination.total = response.data.meta.last_page;
          _this.profilesTotal = response.data.meta.total
        }).catch(error => {

        })
      },

      redirectTo() {
        $('#subscriptionModal').modal('hide')
        this.$router.push({ name: 'choose.plan' })
      },



      getUser(){
        let _this = this;
        axios.get(apiRoute + '/user', this.$store.getters['auth/token']).then(response => {
          this.user = response.data.data;
        }).catch(error => {

        })
      },


      getUserProfileSubscription(){
        let _this = this;
        axios.get(apiRoute + '/profiles/subscription', this.$store.getters['auth/token']).then(response => {
          if(!response.data.subscription) {
              _this.sub = 0
              _this.messages = "To create a new profile, whould you like to upgrade you plan?"
          }else{
            if(!response.data.newProfile && response.data.subscription){

              _this.messages = "You have reached limit for profile creation. Would you like to upgrade you plan?"
              _this.sub = 0
            }else{
              _this.sub = 1
            }
          }
        }).catch(error => {

        })
      },

      alterRemove(id) {
        $("#modal-remove-profile").find('#profileId').val(id);
      },

      openConfModal(type, id) {
        this.typeofAccount = type;
        this.showConfModal = true;
        this.deleteId = id;
      }
    },

    created() {
      bus.$on('deleted', (data) => {
        if(data){
          this.paginateProfile();
          this.showConfModal = false;
          this.getUserProfileSubscription();
        }
      })


      bus.$on('closePaymentModal', (data) => {
        if(data){
          this.showPaymentModal = false;
        }
      })
    },

    mounted() {
      let _this = this;
      this.paginateProfile();
      this.getUser();
      this.getUserProfileSubscription();
      $(document).on('hidden.bs.modal', '#modal-remove-profile', function () {
        _this.paginateProfile();
        _this.getUserProfileSubscription();
      });

    }

  }
</script>

<style scoped lang="scss">
  .title {
    color: #949494;
    text-transform: uppercase;
  }

  table {
    color: #fff;

    tr {
      border-bottom: 1px solid #fff;
      th,
      td {
        border-top: none;
        a {
          text-decoration: none;
          color: #fff;
          transition: 0.3s;
          &.icon-name {
            .profile-img {
              border-radius: 50%;
              width: 40px;
              margin-right: 10px;
            }
            span {
              display: inline-block;
              max-width: 250px;
              white-space: nowrap;
              overflow: hidden;
              text-overflow: ellipsis;
              vertical-align: middle;
              @media (max-width: 1199px) {
                max-width: 220px;
              }
              @media (max-width: 500px) {
                max-width: 110px;
              }
            }
          }

          &:hover {
            transition: 0.3s;
            color: #9d72ff;
          }
          &.disabled {
            cursor: default;
            &:hover {
              color: #fff;
            }
          }
        }
      }
    }
  }

  .profiles-action-button {
    display: inline-block;
    text-decoration: none;
    color: #c1c1c1;
    border: 1px solid #c1c1c1;
    border-radius: 20px;
    padding: 10px 15px;
    text-transform: uppercase;
    transition: 0.3s;
    background: black;
    i {
      margin-left: 15px;
    }
    &:hover {
      text-decoration: none;
      color: #9d72ff;
      border: 1px solid #9d72ff;
      transition: 0.3s;
    }
  }
</style>
