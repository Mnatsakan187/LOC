<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <div class="heading">Confirmation dialog</div>
            <button class="btn close-modal float-left" @click="$emit('close')" v-on:click="close()">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <slot name="body">Question</slot>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn save ml-auto" @click="deleteFunction">Yes</button>
            <button type="button" class="btn cancel" @click="$emit('close')" v-on:click="close()">
              <i class="fas fa-times"></i> No
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "ModalConfirmation",
  props: [
    "deleteId",
    "collectionableId",
    "collectionId",
    "itemType",
    "profileId",
    "projectId",
    "type"
  ],
  data() {
    return {};
  },

  methods: {
    deleteFunction() {
      if (this.type == "profile") {
        axios
          .delete(apiRoute + "/user/profiles/" + this.deleteId)
          .then(response => {
            bus.$emit("deleted", 1);
          })
          .catch(function(error) {});
      } else if (this.type == "collection") {
        axios
          .delete(
            apiRoute + "/user/profiles/1/collections/" + this.deleteId,
            this.$store.getters["auth/token"]
          )
          .then(response => {
            bus.$emit("closeConfDialog", 1);
            bus.$emit("refresh", { refresh: 1 });
          })
          .catch(error => {});
      } else if (this.type == "item") {
        let _this = this;
        axios
          .delete(
            apiRoute +
              "/user/collections/" +
              this.collectionId +
              "/" +
              this.itemType +
              "/" +
              this.collectionableId
          )
          .then(response => {
            bus.$emit("closeConfDialog", 1);
          })
          .catch(function(error) {});
      } else if (this.type == "projects" || this.type == "posts") {
        axios
          .delete(
            apiRoute +
              "/user/" +
              this.type +
              "/" +
              this.deleteId
          )
          .then(response => {
            bus.$emit("closeConfDialog", 1);
            bus.$emit("refresh", 1);
            bus.$emit('notRefresh', 1)
          })
          .catch(function(error) {});
      } else if (this.type == "polls") {
        axios
          .delete(apiRoute + "/user/polls/" + this.deleteId)
          .then(response => {
            bus.$emit("closeConfDialog", 1);
            bus.$emit("refresh", 1);
            bus.$emit('notRefresh', 1)
          })
          .catch(function(error) {});
      } else if (this.type == "media") {
        axios
          .delete(
            apiRoute +
              "/user/projects/" +
              this.projectId +
              "/media/" +
              this.deleteId
          )
          .then(response => {
            bus.$emit("closeConfDialog", 1);
            bus.$emit("refresh", { refresh: 1 });
          })
          .catch(function(error) {});
      } else if (this.type == "group") {
        axios
          .delete(apiRoute + "/user/groups/" + this.deleteId)
          .then(response => {
            bus.$emit("refresh", 1);
          })
          .catch(function(error) {});
      } else if (this.type == "link") {
        bus.$emit("deleteLink", 1);
      } else if (this.type == "image") {
        bus.$emit("deleteImage", 1);
      } else if (this.type == "answer") {
        bus.$emit("deleteAnswer", 1);
      }
    },

    close() {
      bus.$emit("closeConfDialog", 1);
    }
  },

  created() {},

  mounted() {}
};
</script>

<style scoped lang="scss">
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
  @media (max-width: 600px) {
    vertical-align: bottom;
  }
  .modal-container {
    width: 600px;
    margin: 0px auto;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
    color: #323232;
    @media (max-width: 600px) {
      width: 100%;
    }
    .modal-header {
      border: none;
      padding-bottom: 0;
    }
    .heading {
      font-family: EncodeSansSemiBold;
      font-size: 1.4rem;
    }
    input {
      border: none;
    }
    .close-modal {
      font-size: 1.2rem;
    }
    .modal-footer {
      background-color: #474747;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 0;
      justify-content: flex-start;
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
}

/* Modal transition styles */
.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
  @media (max-width: 600px) {
    transform: translateY(200px);
  }
}
</style>
