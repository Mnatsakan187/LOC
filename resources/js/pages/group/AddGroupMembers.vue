<template>
  <div class="container-fluid create">
    <div class="row create-white">
      <div
        class="col-sm-12 col-md-8 col-lg-6 col-xl-4 offset-md-2 offset-lg-3 offset-xl-4 mt-5 mb-5"
      >
        <div class="header">
          <div class="title">
            <div class="heading">{{group.name}}</div>
            <div class="sub-title">members</div>
          </div>

          <router-link class="btn close-button" :to="{ path: prevRoute}">
            <i class="fas fa-times"></i>
          </router-link>
        </div>
        <div class="content mt-5">
          <form onsubmit="return false">
            <div class="form-group">
              <label class="sr-only" for="enterUrl">Add Members</label>
              <v-select
                id="enterUrl"
                multiple
                class="members-select"
                v-model="group.user"
                :options="users"
                label="id"
                placeholder="Add Members"
                name="members"
                v-validate="'required'"
              >
                <template v-slot:selected-option="member">
                  <div class="option-img-name">
                    <img
                      v-if="member.avatarUri"
                      :src="'/storage/avatarImage/' + member.id + '/' + member.avatarUri"
                    />
                    <img v-else src="/images/user8-128x128.png" />
                    <span class="name">{{member.name}}</span>
                  </div>
                </template>
                <template v-slot:option="member">
                  <div class="option-img-name">
                    <img
                      v-if="member.avatarUri"
                      :src="'/storage/avatarImage/' + member.id + '/' + member.avatarUri"
                    />
                    <img v-else src="/images/user8-128x128.png" />
                    <div class="name-title">
                      <span class="name">{{member.name}}</span>
                      <span class="title">{{member.creativeTitle}}</span>
                    </div>
                  </div>
                </template>
              </v-select>
              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('members')"
              >{{ errors.first('members') }}</span>
            </div>
            <button class="btn btn-add-members" @click="addMembers">Add Members</button>
          </form>
          <div class="no-members mt-5" v-if="!members.length">
            You do not have any group members yet!
            <br />Please, add group members by typing their name in "Add members" select box.
          </div>
          <div class="members mt-5">
            <MemberItem
              v-for="member in members"
              :key="member.id"
              v-bind:member="member"
              :group="group"
            ></MemberItem>
          </div>
        </div>
      </div>
    </div>
    <div class="row create-gray">
      <div class="col-sm-12 col-md-6 offset-md-3">
        <div class="action-buttons">
          <router-link class="btn cancel" :to="{ path: prevRoute}">
            <i class="fas fa-times"></i> Cancel
          </router-link>

          <router-link type="button" class="btn save" :to="{ path: prevRoute}">Save</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import "vue-select/dist/vue-select.css";
import MemberItem from "../../shared/memberItem/MemberItem.vue";
export default {
  name: "AddGroupMembers",
  middleware: "auth",
  data() {
    return {
      selectedMembers: [],
      members: [],
      prevRoute: "",
      users: [],
      group: {
        name: "",
        description: "",
        isVisible: 1,
        user: []
      },
      submitted: false
    };
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (from.path != "/") {
        vm.prevRoute = from.path;
      } else {
        vm.prevRoute = "/home";
      }
    });
  },

  components: {
    MemberItem
  },

  methods: {
    getGroup() {
      let _this = this;
      axios
        .get(
          apiRoute + "/user/groups/" + this.groupId,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          this.group = response.data.data;
        })
        .catch(error => {});
    },

    getGroupMembers() {
      let _this = this;
      axios
        .get(
          apiRoute + "/user/groups/" + this.groupId + "/members",
          this.$store.getters["auth/token"]
        )
        .then(response => {
          this.members = response.data.data;
          this.getUser();
        })
        .catch(error => {});
    },

    getUser() {
      let _this = this;
      axios
        .get(apiRoute + "/users", this.$store.getters["auth/token"])
        .then(response => {
          function comparer(otherArray) {
            return function(current) {
              return (
                otherArray.filter(function(other) {
                  return other.id == current.id;
                }).length == 0
              );
            };
          }

          var onlyInA = response.data.data.filter(comparer(this.members));
          var onlyInB = this.members.filter(comparer(response.data.data));
          _this.users = [];
          onlyInA.concat(onlyInB).forEach((value, key) => {
            _this.users.push({
              id: value.id,
              name: value.firstName + " " + value.lastName,
              creativeTitle: value.creativeTitle,
              avatarUri: value.avatarUri
            });
          });
        })
        .catch(error => {});
    },

    deleteMember(groupId, memberId) {
      let _this = this;
      axios
        .delete(apiRoute + "/user/groups/" + groupId + "/members/" + memberId)
        .then(response => {
          _this.getGroup();
          _this.getGroupMembers();
        })
        .catch(function(error) {});
    },

    addMembers() {
      let _this = this;
      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (valid) {
          axios
            .post(
              apiRoute + "/user/groups/" + this.groupId + "/members",
              this.group,
              this.$store.getters["auth/token"]
            )
            .then(response => {
              _this.getGroup();
              _this.getGroupMembers();
              _this.submitted = false;
            })
            .catch(error => {});
        }
      });
    }
  },

  created() {
    this.groupId = this.$route.params.id;
    this.getGroup();
    this.getGroupMembers();

    bus.$on("refresh", data => {
      if (data == 1) {
        this.getGroup();
        this.getGroupMembers();
      }
    });
  },

  mounted() {}
};
</script>
<style lang="scss">
.members-select {
  .vs__dropdown-toggle {
    border: none !important;
    background-color: #e5e5e5;
    input {
      color: #323232;
    }
  }
  .vs__selected {
    font-size: 1.2rem;
    color: #323232 !important;
  }
  .vs__clear,
  .vs__open-indicator {
    fill: #323232 !important;
  }
  .vs__dropdown-menu {
    background-color: #e5e5e5 !important;
    .vs__dropdown-option {
      color: #323232;
      &:hover,
      &:focus,
      &.vs__dropdown-option--highlight {
        color: #fff;
        background-color: #323232 !important;
      }
    }
  }
  .option-img-name {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 15px;
    }
    .name {
      display: block;
      font-size: 1.1rem;
      font-family: EncodeSansSemiBold;
    }
    .title {
      display: block;
      font-size: 1rem;
    }
  }
}
</style>

<style scoped lang="scss">
.create {
  background-color: #474747;
  @media (max-width: 767px) {
    padding-bottom: 60px;
  }
}
.create-white {
  padding-left: 20px;
  padding-right: 20px;
  background: #fff;
  color: #474747;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  .heading {
    font-family: EncodeSansSemiBold;
    font-size: 1.6rem;
    color: #474747;
  }
  .sub-title {
    text-transform: uppercase;
  }
  .close-button {
    color: #474747;
    font-size: 1.6rem;
  }
}
input,
textarea {
  font-size: 1.2rem;
  color: #474747;
  border: none;
  background-color: #e5e5e5;
  &::placeholder {
    color: #474747;
  }
}
.action-buttons {
  background-color: #474747;
  text-align: center;
  padding-top: 20px;
  padding-bottom: 20px;
  .cancel {
    color: #fff;
    font-size: 1.2rem;
    &:hover {
      color: #b9b9b9;
    }
  }
  .save {
    color: #474747;
    font-size: 1.2rem;
    text-decoration: none;
    background-color: #fff;
    padding: 10px 60px;
    border-radius: 30px;
    margin-left: 20px;
    @media (max-width: 767px) {
      padding: 5px 10px;
    }
    &:hover {
      opacity: 0.7;
      transition: 0.3s;
    }
  }
}
.btn-add-members {
  color: #474747;
  border: 1px solid #474747;
  border-radius: 20px;
  padding: 5px 20px;
  font-family: EncodeSansSemiBold;
  transition: 0.5s;
  &:hover {
    color: #9d72ff;
    border: 1px solid #9d72ff;
    transition: 0.5s;
  }
}
.no-members {
  text-align: center;
  font-size: 1.2rem;
}
</style>
