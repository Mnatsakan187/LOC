<template>
  <transition name="modal">
    <div class="modal">
      <div class="modal-mask"></div>
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <button class="btn close-modal float-right" @click="$emit('close')">
              <i class="fas fa-times"></i>
            </button>
            <div class="heading">{{group.name}}</div>
            <div class="sub-title">members</div>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label class="sr-only" for="enterUrl">Add Members</label>
                <v-select
                  multiple
                  class="modal-members-select"
                  v-model="selectedMembers"
                  :options="members"
                  label="id"
                  placeholder="Add Members"
                >
                  <template v-slot:selected-option="member">
                    <div class="option-img-name">
                      <img src="/images/creator1.png" />
                      <span class="name">{{member.name}}</span>
                    </div>
                  </template>
                  <template v-slot:option="member">
                    <div class="option-img-name">
                      <img src="/images/creator1.png" />
                      <div class="name-title">
                        <span class="name">{{member.name}}</span>
                        <span class="title">{{member.creativeTitle}}</span>
                      </div>
                    </div>
                  </template>
                </v-select>
              </div>
              <button class="btn btn-add-members">Add Members</button>
            </form>
            <div class="no-members mt-5">
              You do not have any group members yet!
              <br />Please, add group members by typing their name in "Add members" select box.
            </div>
            <div class="members mt-5">
              <MemberItem v-for="member in members" :key="member.id" v-bind:member="member"></MemberItem>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn cancel" @click="$emit('close')">
              <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn save">Save</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import MemberItem from "../memberItem/MemberItem.vue";
import "vue-select/dist/vue-select.css";
export default {
  name: "ModalAddGroupMembers",
  props: ["members", "group"],
  components: {
    MemberItem
  },
  data() {
    return {
      selectedMembers: [],
      users: {}
    };
  },
  methods: {
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
          var onlyInA = response.data.data.filter(comparer(_this.members));
          var onlyInB = _this.members.filter(comparer(response.data.data));
          _this.users = onlyInA.concat(onlyInB);
        })
        .catch(error => {});
    },

    deleteMember(groupId, memberId) {
      let _this = this;
      axios
        .delete(apiRoute + "/user/groups/" + groupId + "/members/" + memberId)
        .then(response => {
          _this.getGroup();
        })
        .catch(function(error) {});
    },

    addMembers() {
      let _this = this;
      this.submitted = true;
      axios
        .post(
          apiRoute + "/user/groups/" + this.group.id + "/members",
          this.group,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.getGroup();
          _this.getUser();
          _this.submitted = false;
        })
        .catch(error => {});
    }
  },
  mounted() {
    this.getUser();

    // this.members = [
    //   {
    //     id: 1,
    //     name: "John Smith",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 2,
    //     name: "John Smith2",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 3,
    //     name: "John Smith3",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 4,
    //     name: "John Smith4",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 5,
    //     name: "John Smith5",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 6,
    //     name: "John Smith6",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 7,
    //     name: "John Smith7",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 8,
    //     name: "John Smith8",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 9,
    //     name: "John Smith9",
    //     creativeTitle: "visual artist"
    //   },
    //   {
    //     id: 10,
    //     name: "John Smith10",
    //     creativeTitle: "visual artist"
    //   }
    // ];
  }
};
</script>
<style lang="scss">
.modal-members-select {
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
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  @media (max-width: 767px) {
    align-items: flex-end;
  }
  .modal-mask {
    background-color: rgba(0, 0, 0, 0.5);
    position: absolute;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
  }
  .modal-wrapper {
    position: relative;
    width: 700px;
    @media (max-width: 767px) {
      width: 100vw;
      height: 100vh;
      overflow-y: scroll;
    }
  }
  .modal-container {
    background: #fff;
    color: #323232;
    @media (max-width: 767px) {
      width: 100vw;
      height: 100vh;
      overflow-y: scroll;
      margin-bottom: 150px;
    }
  }
  .modal-header {
    border: none;
    display: block;
    .heading {
      font-family: EncodeSansSemiBold;
      font-size: 1.6rem;
      color: #474747;
    }
    .sub-title {
      text-transform: uppercase;
    }
    .close-modal {
      font-size: 1.2rem;
    }
  }
  .modal-body {
    @media (min-width: 767px) {
      max-height: 50vh;
      overflow-y: scroll;
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
  }

  .modal-footer {
    background-color: #474747;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
    @media (max-width: 767px) {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
    }
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
      &:hover {
        opacity: 0.7;
        transition: 0.3s;
      }
    }
  }
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s;
}
.modal-enter,
.modal-leave-to {
  opacity: 0;
}
</style>
