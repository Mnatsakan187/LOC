<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <div class="heading">Upload Image</div>
            <button class="btn close-modal float-left" @click="$emit('close')">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="sr-only" for="inputUploadImage">Upload Url</label>
              <input
                v-validate="'required'"
                v-if="cropper == 'cropper'"
                id="inputUploadImage"
                type="file"
                name="image"
                accept="image/*"
                @change="setImage"
              />
              <input
                v-validate="'required'"
                v-if="cropper == 'post'"
                id="inputUploadImage"
                type="file"
                name="image"
                accept="image/*"
                @change="setImage"
              />
              <input
                v-validate="'required'"
                v-if="cropper == 'cropperBg'"
                id="inputUploadImages"
                type="file"
                name="image"
                accept="image/*"
                @change="setBackground"
              />
            </div>
            <div
              class="invalid-feedback"
              v-if="submitted && errors.has('image')"
            >{{ errors.first("image") }}</div>

            <div v-if="imgSrc && cropper == 'post'">
              <img :src="imgSrc" class="post-img" />
            </div>

            <div v-if="imgSrc && cropper == 'cropper'">
              <vue-cropper
                ref="cropper"
                :guides="false"
                :view-mode="0"
                drag-mode="crop"
                :auto-crop-area="0.5"
                :background="true"
                :aspectRatio="1"
                :src="imgSrc"
                alt="Source Image"
                :touchend="true"
                :responsive="true"
                :img-style="{'max-height': '400px','max-width': '100%'}"
              ></vue-cropper>
            </div>

            <div v-if="urlBackgroundImage && cropper == 'cropperBg'">
              <vue-cropper
                ref="crop"
                :guides="false"
                :view-mode="0"
                drag-mode="crop"
                :auto-crop-area="0.5"
                :background="true"
                :aspectRatio="6/1"
                :src="urlBackgroundImage"
                alt="Source Image"
                :touchend="true"
                :responsive="true"
                :img-style="{'max-height': '400px','max-width': '100%'}"
              ></vue-cropper>
            </div>

            <form>
              <div class="form-group max-width creditsRight">
                <label for="credits">Created by:</label>
                <input
                  type="text"
                  class="form-control"
                  id="credits"
                  v-model="createdBy"
                  placeholder="Add artist credits"
                />
              </div>

              <div class="form-group max-width">
                <label class="sr-only" for="enterUrl">Add Members</label>
                <v-select
                  multiple
                  class="members-select"
                  v-model="selectedMembers"
                  :options="coCreators"
                  label="id"
                  placeholder="Or select connected LOC profiles"
                >
                  <template v-slot:selected-option="member">
                    <div class="option-img-name">
                      <img
                        v-if="member.avatarUri"
                        :src="'/storage/avatarImage/' + member.id + '/' + member.avatarUri"
                      />
                      <img v-else src="/images/user8-128x128.png" />
                      <span class="name">{{member.creativeTitle}}</span>
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
                        <span class="name">{{member.creativeTitle}}</span>
                      </div>
                    </div>
                  </template>
                </v-select>
              </div>
            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn cancel" @click="$emit('close')">
              <i class="fas fa-times"></i> Cancel
            </button>
            <button
              v-if="cropper == 'cropper'"
              type="button"
              class="btn save"
              @click="cropImage"
            >Save</button>
            <button v-if="cropper == 'post'" type="button" class="btn save" @click="cropImage">Save</button>
            <button
              v-if="cropper == 'cropperBg'"
              type="button"
              class="btn save"
              @click="cropBackground"
            >Save</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import VueCropper from "vue-cropperjs";
import { helpers } from "../../mixins/helpers";
import "vue-select/dist/vue-select.css";
export default {
  components: { VueCropper },
  name: "ModalUploadImage",
  props: ["imgSrcCrouped", "cropper", "backgroundSrcCrouped"],
  mixins: [helpers],
  data() {
    return {
      next: 4,
      prev: 2,
      submitted: false,
      day: "",
      month: "",
      year: "",
      bio: "",
      months: "",
      years: "",
      currentYear: 0,
      daysInMonth: 31,
      profileImage: "",
      backgroundImage: "",
      urlProfileImage: "",
      urlBackgroundImage: "",
      location: "",
      creativeTitle: "",
      imgSrc: "",
      cropImg: "",
      croupBackgroundUrl: "",
      cities: [],
      file: "",
      coCreators: [],
      selectedMembers: [],
      createdBy: ""
    };
  },
  methods: {
    setImage(e) {
      const file = e.target.files[0];
      this.file = e.target.files[0];
      if (!file.type.includes("image/")) {
        alert("Please select an image file");
        return;
      }

      if (typeof FileReader === "function") {
        const reader = new FileReader();

        reader.onload = event => {
          this.imgSrc = event.target.result;
          // rebuild cropperjs with the updated source
          if (this.$refs.cropper != undefined) {
            this.$refs.cropper.replace(event.target.result);
          }
        };

        reader.readAsDataURL(file);
      } else {
        alert("Sorry, FileReader API not supported");
      }
    },

    cropImage() {
      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (this.cropper != "post") {
          if (valid || this.imgSrc) {
            this.$emit("close");
            this.$emit("images", {
              profileImage: this.dataURLtoFile(
                this.$refs.cropper.getCroppedCanvas().toDataURL(),
                "file"
              ),
              cropImg: this.$refs.cropper.getCroppedCanvas().toDataURL(),
              createdBy: this.createdBy,
              selectedMembers: this.selectedMembers
            });
          }
        } else {
          if (valid || this.imgSrc) {
            this.$emit("close");
            this.$emit("images", {
              profileImage: this.file,
              cropImg: this.imgSrc,
              createdBy: this.createdBy,
              selectedMembers: this.selectedMembers
            });
          }
        }
      });
    },

    setBackground(e) {
      const file = e.target.files[0];
      if (!file.type.includes("image/")) {
        alert("Please select an image file");
        return;
      }

      if (typeof FileReader === "function") {
        const reader = new FileReader();

        reader.onload = event => {
          this.urlBackgroundImage = event.target.result;
          // rebuild cropperjs with the updated source
          if (this.$refs.crop != undefined) {
            this.$refs.crop.replace(event.target.result);
          }
        };

        reader.readAsDataURL(file);
      } else {
        alert("Sorry, FileReader API not supported");
      }
    },

    cropBackground() {
      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (valid || this.urlBackgroundImage) {
          this.$emit("close");
          this.$emit("bgImages", {
            backgroundImage: this.dataURLtoFile(
              this.$refs.crop.getCroppedCanvas().toDataURL(),
              "file"
            ),
            croupBackgroundUrl: this.$refs.crop.getCroppedCanvas().toDataURL()
          });
        }
      });
    },

    getCoCreators() {
      axios
        .get(apiRoute + "/user/co-creators", this.$store.getters["auth/token"])
        .then(response => {
          let _this = this;
          _this.coCreators = response.data.data;
        })
        .catch(error => {});
    }
  },

  mounted() {
    if (this.imgSrcCrouped) {
      this.imgSrc = this.imgSrcCrouped;
    }

    if (this.backgroundSrcCrouped) {
      this.urlBackgroundImage = this.backgroundSrcCrouped;
    }

    this.getCoCreators();
  }
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
    max-height: calc(100vh - 210px);
    overflow-y: auto;
    overflow-x: hidden;
    @media (max-width: 600px) {
      width: 100%;
      max-height: calc(100vh - 100px);
    }

    .modal-header {
      border: none;
    }
    .heading {
      font-family: EncodeSansSemiBold;
      font-size: 1.4rem;
    }
    .form-group {
      max-width: 250px;
    }
    input {
      border: none;
      font-size: 1.2em;
      padding: 10px 0;
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

  @media (max-width: 600px) {
    .modal-header {
      padding-top: 10px;
      padding-bottom: 0px;
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

.modal-body {
  text-align: center;
}

.post-img {
  max-width: 100%;
  max-height: 400px;
}

#credits {
  font-size: 1em;
  padding-left: 10px;
  color: #474747;
  border: none;
  background-color: #e5e5e5;
  &::placeholder {
    color: #474747;
  }
}

.max-width {
  max-width: 100% !important;
}

.creditsRight {
  text-align: left;
}

.vs--searchable .vs__dropdown-toggle {
  border: 0px solid rgba(60, 60, 60, 0.26);
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

.members-select .vs__dropdown-toggle input {
  color: #323232;
}
</style>









