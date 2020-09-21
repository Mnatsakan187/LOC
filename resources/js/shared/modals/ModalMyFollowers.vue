<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <div class="heading">My followers</div>
            <button class="btn close-modal float-left" @click="$emit('close')">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <div class="body">
              <div class="followers-list">
                <div v-for="follower in  followers" class="follower">
                  <img v-if="follower.avatarUri" :src="'/storage/avatarImage/' + value.id + '/' + value.avatarUri" />
                  <img v-else  src="/images/user8-128x128.png"/>
                  <span class="follower-name">{{follower.firstName}}  {{follower.lastName}}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn cancel mx-auto" @click="$emit('close')">
              <i class="fas fa-times"></i> Dismiss
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "ModalMyFollowers",
  props:['followers'],
  mounted(){

  }
};
</script>

<style scoped lang="scss">
.body {
  max-height: 400px;
  overflow-y: scroll;
}
.followers-list {
  .follower {
    padding: 10px 0;
    img {
      width: 50px;
      border-radius: 50%;
      margin-right: 10px;
    }
  }
}
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

.bolder{
  text-align: center;
  font-size: 1.2rem;
  font-family: EncodeSansSemiBold;
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
}
</style>
