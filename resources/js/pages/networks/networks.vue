<template>
  <div class="container hud">
    <!-- Main content-->
    <div class="container">
      <div class="row mt-5">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
          <h1 class="d-none d-md-block">My network</h1>
          <div v-if="networks.length == 0" class="form-group mt-3">
            <label class="select-tag">Add creators/users to Network</label>
            <multiselect
              v-validate="'required'"
              tag-placeholder="Add this as new tag"
              placeholder="Search"
              :options="creatorsUsers"
              v-model="users"
              track-by="id"
              :multiple="true"
              :searchable="true"
              label="firstName" name="members">
            </multiselect>
            <span class="error-message" v-if="submitted && errors.has('members')">{{ errors.first('members') }}</span>
          </div>

          <div v-else class="form-group mt-3">
            <label class="select-tag">Add creators/users to Network</label>
            <multiselect
              v-validate="'required'"
              tag-placeholder="Add this as new tag"
              placeholder="Search"
              :options="comparerCreators"
              v-model="users"
              track-by="id"
              :multiple="true"
              :searchable="true"
              label="firstName" name="members">
            </multiselect>
            <span class="error-message" v-if="submitted && errors.has('members')">{{ errors.first('members') }}</span>
          </div>

          <!-- Navigation Buttons -->
          <div class="mt-2">
            <a class="btn btn-primary" @click="addNetwork" role="button">Add</a>
            <router-link class="btn btn-link" :to="{ path: prevRoute}" >Cancel</router-link>
          </div>
        </div>
      </div>
      <div class="row mt-5">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
          <div v-if="!networks.length" class="no-team-members">
            There are no team networks yet.<br>
            Start adding your networks!
          </div>
        </div>
      </div>
    </div>
    <div class="row"  v-if="networks.length > 0">
      <div class="col">
        <div >
          <div  class="container" >

            <div v-for="network in networks" class="creators-content">
              <div class="action-block clearfix">
                <div class="action-status float-left align-items-center">
                  <div>
                    <img v-if="network.avatarUri" :src="'/storage/avatarImage/' + network.id + '/' + network.avatarUri"  alt="default" class="menu-profile-img">
                    <img v-else src="/images/user8-128x128.png"  alt="default" class="menu-profile-img">

                    <div class="right ml-2 pb-0">
                      <p class="name-of-creator">{{network.firstName}}  {{network.lastName}}</p>
                      <p class="create-title" v-if="network.accountType == 1">Creator</p>
                      <p class="create-title" v-else>User</p>
                    </div>
                  </div>
                </div>
                <div class="action-buttons float-right">
                  <div class="dropdown  edit-content-creator float-right">
                    <button class="btn dropdown-toggle" type="button" id="dropdownProject" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                      <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownProject">
                      <a class="dropdown-item"  data-toggle="modal" :data-target="'#modal-remove-network-'+network.id">
                        Delete contact
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" :id="'modal-remove-network-'+network.id" tabindex="-1" role="dialog"
                   aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="header-section header-section-white">
                      <a class="fa fa-times right pointer color-light-blue" data-dismiss="modal"></a>
                      <h2>Are you sure you want to remove this profile </h2>
                    </div>

                    <div class="modal-footer text-center">
                      <button type="button" class="btn btn-large btn-light-grey"
                              data-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-large btn-red"
                              @click="deleteNetwork(network.id)">Delete</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'create',
    middleware: 'auth',
    data () {
      return {
        creatorsUsers: [],
        prevRoute: '',
        submitted: '',
        profileId: '',
        users:[],
        networks:[],
        comparerCreators: [],

      }
    },

    beforeRouteEnter(to, from, next) {
      next(vm => {
        if(from.path != '/') {
          vm.prevRoute = from.path;
        }else{
          vm.prevRoute = '/home';
        }
      })
    },

    methods: {
      getUser() {
        let _this = this;
        axios.get(apiRoute + '/users?exception='+1,  this.$store.getters['auth/token']).then(response => {
          _this.creatorsUsers = response.data.data;
        }).catch(error => {

        });
      },

      deleteNetwork(userId) {
        let _this = this;
        axios.delete(apiRoute+ '/user/networks/'+userId).then(response => {
          _this.getUser();
          _this.getNetworks();
          $('#modal-remove-network-' + userId).modal('hide');
        }).catch(function (error) {

        });
      },


      addNetwork() {
        let _this = this;
        this.submitted = true;
        this.$validator.validate().then(valid => {
          if (valid) {
            axios.post(apiRoute + '/user/networks', {networkUserId: this.users},  this.$store.getters['auth/token']).then(response => {
              _this.getUser();
              _this.getNetworks();
              _this.users = [];
              _this.submitted =false;
            }).catch(error => {

            });
          }
        });
      },

      getNetworks(){
        let _this = this
        axios.get(apiRoute + '/user/networks',   this.$store.getters['auth/token']).then(response => {
          _this.networks = response.data.data

          function comparer(otherArray){
            return function(current){
              return otherArray.filter(function(other){
                return other.id == current.id
              }).length == 0;
            }
          }
          var onlyInA = response.data.data.filter(comparer(_this.creatorsUsers));
          var onlyInB = _this.creatorsUsers.filter(comparer(response.data.data));
          _this.comparerCreators = onlyInA.concat(onlyInB);
        }).catch(error => {

        });
      }

    },

    created () {
      this.getUser();
      this.getNetworks();
    },

    mounted () {

    }
  }
</script>

<style scoped>
  /* HUD Styles START */

  .tile img {
    width: 100%;
    height: 200px;
  }

  .search-empty i {
    margin-top: 50px;
    margin-bottom: 10px;
    color: #ccc;
    font-size: 5rem;
  }

  .search-empty p {
    color: #ccc;
    font-size: 1.2rem;
  }

  .edit-content a i {
    color: #159BD5;
  }

  .edit-content .dropdown-toggle i {
    color: #159BD5;
  }

  .edit-content .dropdown-toggle::after {
    display: none;
  }

  .edit-content .dropdown-menu {
    min-width: 8rem;
    text-align-last: left !important;
  }

  .edit-content-creator a i {
    color: #159BD5;
  }

  .edit-content-creator .dropdown-toggle i {
    color: #159BD5;
  }

  .edit-content-creator .dropdown-toggle::after {
    display: none;
  }

  .edit-content-creator .dropdown-menu {
    min-width: 8rem;
    text-align-last: left !important;
  }

  .edit-content-creator{
    top: 10px;
  }

  .toggle-button input {
    position: absolute;
    clip: rect(0, 0, 0, 0);
    pointer-events: none;
  }

  .menu-profile-img {
    border-radius: 50%;
    width: 50px;
    height: 50px;
  }

  .name-of-creator {
    margin-bottom: 1px;
    font-size: 16px;
    margin-top: 3px;
  }

  .creators-content {
    border-top: 1px solid #e5e5e5;
    padding-top: 12px;

  }

  .collection-content .collection .more-link i {
    text-decoration: none;
    color: #9A9A9A;
    padding: 20px 20px;
    background-color: #F2F2F2;
    border-radius: 50%;
    cursor: pointer;
  }


  .collection-content .collection .more-link i:hover {
    background-color: #e5e5e5;
  }



  .collection-action .dropdown-toggle i {
    color: #159BD5;
  }

  .collection-action .dropdown-toggle::after {
    display: none;
  }

  .collection-action .dropdown-menu {
    min-width: 8rem;
    text-align-last: left !important;
  }


  .modal-footer {
    background: #fafbfc;
    text-align: center;
    border-top: none;
    padding: 30px;
    border-radius: 0 0 10px 10px;
  }

  .modal-footer .btn-light-grey {
    color: #a1acb5;
  }
  .btn-red {
    background-color: #f69798;
  }
  .btn-large {
    padding: 13px 30px;
    color: white;
    font-weight: 600;
    font-size: 14px;
    border-radius: 5px;
  }

  .btn-large {
    padding: 13px 30px;
    color: white;
    font-weight: 600;
    font-size: 14px;
    border-radius: 5px;
  }

  .btn-light-grey {
    background-color: #e9edef;
  }

  .modal-footer {
    background: #fafbfc;
    text-align: center;
    border-top: none;
    padding: 30px;
    border-radius: 0 0 10px 10px;
  }

  .modal-content {
    border-radius: 10px;
  }

  .modal-dialog .header-section, .modal-dialog-wide .header-section {
    display: block;
  }

  .modal-content {
    border-radius: 5px;
    box-shadow: none;
    border: 0;
  }

  .header-section.header-section-white {
    background: white;
  }

  .header-section.header-section-white {
    background: white;
  }

  .header-section {
    background: #fafbfc;
    padding: 30px;
    text-align: center;
    border-radius: 10px 10px 0 0;
  }

  .modal-dialog .header-section h2, .modal-dialog-wide .header-section h2 {
    font-weight: 600;
    color: #4c5962;
  }

  .header-section {
    background: #fafbfc;
    padding: 30px;
    text-align: center;
    border-radius: 10px 10px 0 0;
  }

  .color-light-blue, .color-light-blue::before {
    color: #c5daea;
  }

  .color-light-blue, .color-light-blue::before {
    color: #c5daea;
  }


  .modal-dialog .header-section h1, .modal-dialog .header-section h2, .modal-dialog-wide .header-section h1, .modal-dialog-wide .header-section h2 {
    font-size: 16px;
    font-weight: 400;
    margin: 0;
    color: #77828b;
    width: 100%;
  }

  .text-center{
    margin: auto;
  }


  .widget-pagination > ul li a {
    width: 9px;
    height: 9px;
    margin: 0 5px;
    border-radius: 100px;
    background-color: #DEE4E9;
    display: block;
    cursor: pointer;
  }

  .widget-pagination > ul li {
    display: inline-block;
    background: none;
    padding: 0;
    width: auto;
  }

  .widget-pagination ul {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
  }

  .no-team-members{
    text-align: center;
    color: #999;
    font-size: 1.2rem;
  }

</style>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

