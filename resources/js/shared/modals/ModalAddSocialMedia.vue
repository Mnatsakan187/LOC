<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <div class="heading">Enter your social media link</div>
            <button type="button" class="btn close-x float-left" @click="close">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="sr-only" for="socialMediaLink">Enter URL</label>
              <input
                type="text"
                required
                v-model="socialLink"
                name="social media link"
                class="form-control"
                v-validate="{ url: { required: true, require_protocol: true } }"
                id="socialMediaLink"
                placeholder="Enter Url"
              />
              <input type="hidden" id="socialName" class="form-control" />
              <div
                class="invalid-feedback"
                v-if="submitted && errors.has('social media link')"
              >{{ errors.first("social media link") }}</div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn cancel" @click="close">
              <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn save" @click="save()">Save</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "ModalAddSocialMedia",
  middleware: "auth",
  data() {
    return {
      socialLink: "",
      submitted: false
    };
  },

  methods: {
    save() {
      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (valid) {
          bus.$emit("save", { socialLink: this.socialLink });
          this.socialLink = "";
          this.submitted = false;
        }
      });
    },

    close() {
      this.submitted = false;
      bus.$emit("close", 1);
    }
  },

  mounted() {},

  created() {}
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
    }
    .heading {
      font-family: EncodeSansSemiBold;
      font-size: 1.4rem;
    }
    input {
      border: none;
      background-color: #e5e5e5;
    }
    .close-x {
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
