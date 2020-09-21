<template>
  <div class="container-fluid create">
    <div class="row create-white">
      <div
        class="col-sm-12 col-md-8 col-lg-6 col-xl-4 offset-md-2 offset-lg-3 offset-xl-4 mt-5 mb-5"
      >
        <!--Header-->
        <div class="header">
          <div class="heading" v-if="!projectId">New Project</div>
          <div class="heading" v-else>Edit Project</div>
          <router-link :to="{ path: prevRoute}" class="btn close-button">
            <i class="fas fa-times"></i>
          </router-link>
        </div>
        <div class="content mt-5">
          <!--Form begins-->
          <form>
            <div class="project-name-avatar">
              <!-- Project avatar -->
              <div class="project-avatar" @click="showModal = true">
                <i class="fas fa-user"></i>
                <i class="fas fa-plus"></i>
                <img v-if="avatarCropImg" :src="avatarCropImg" class="profile-picture-image" />
              </div>
              <!-- Project name -->
              <div class="project-name">
                <div class="form-group">
                  <label class="sr-only" for="projectName">Project Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="projectName"
                    placeholder="Project name"
                    v-model="saveProjects.name"
                    v-validate="'required|max:45'"
                    name="project name"
                  />
                  <span
                    class="invalid-feedback"
                    v-if="submitted && errors.has('project name')"
                  >{{ errors.first('project name') }}</span>
                  <span
                    class="invalid-feedback"
                    v-if="error.get('name') && !errors.has('project name')"
                  >{{error.get('name')}}</span>
                </div>
              </div>
            </div>
            <!-- Select profile -->
            <div class="form-group mt-4">
              <label class="sr-only">Select profile</label>
              <v-select
                class="project-select"
                :options="profiles"
                label="creativeTitle"
                placeholder="Select Profile"
                v-validate="'required'"
                v-model="saveProjects.profile"
                name="profile"
                id="profile"
              >
                <template v-slot:selected-option="profile">
                  <div class="option-img-name">
                    <img
                      v-if="profile.avatarUri"
                      :src="'/storage/profiles/profilePictureImage/' + profile.id + '/' + profile.avatarUri"
                    />
                    <img v-else src="/images/user8-128x128.png" />
                    <span class="name">{{profile.creativeTitle}}</span>
                  </div>
                </template>
                <template v-slot:option="profile">
                  <div class="option-img-name">
                    <img
                      v-if="profile.avatarUri"
                      :src="'/storage/profiles/profilePictureImage/' + profile.id + '/' + profile.avatarUri"
                    />
                    <img v-else src="/images/user8-128x128.png" />
                    <div class="name-title">
                      <span class="name">{{profile.user.firstName}} {{profile.user.lastName}}</span>
                      <span class="title">{{profile.creativeTitle}}</span>
                    </div>
                  </div>
                </template>
              </v-select>
              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('profile')"
              >{{ errors.first('profile') }}</span>
            </div>
            <!-- Project cover image -->
            <div class="add-project-cover mt-4" @click="showModalBgModal = true">
              <h5 v-if="!cropImg">Add project cover</h5>
              <p v-if="!cropImg">This image will be the cover of your project detail page.</p>
              <i v-if="!cropImg" class="fas fa-camera"></i>
              <img v-if="cropImg" :src="cropImg" class="background-image" />
            </div>
            <!-- Project type -->
            <div class="select-project-type mt-4">
              <div class="label">Select project type:</div>
              <div class="types">
                <a v-bind:class="{'active focus': saveProjects.type == 0}" @click="getType(0)">
                  <div class="circle w">W</div>
                </a>
                <a v-bind:class="{'active focus': saveProjects.type == 1}" @click="getType(1)">
                  <div class="circle a">A</div>
                </a>
                <a v-bind:class="{'active focus': saveProjects.type == 2}" @click="getType(2)">
                  <div class="circle v">V</div>
                </a>
              </div>
            </div>
            <span class="invalid-feedback" v-if="validationMessages">{{validationMessages}}</span>

            <div class="form-group mt-4">
              <label class="sr-only" for="projectDescription">Project Description</label>
              <ckeditor
                id="projectDescription"
                v-validate="'required|max:1000'"
                name="description"
                v-model="saveProjects.description"
                :editor="editor"
                :config="editorConfig"
              ></ckeditor>
              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('description')"
              >{{ errors.first('description') }}</span>
              <span class="invalid-feedback" v-text="error.get('description')"></span>
            </div>
            <!-- Project tags -->
            <div class="form-group">
              <label class="sr-only">Add Tags</label>
              <v-select
                class="tags-select"
                v-model="tagsValue"
                :options="tags"
                placeholder="Add Tags"
                label="name"
                name="tag"
                track-by="name"
                :multiple="true"
                :taggable="true"
                @input="addTag"
              ></v-select>
            </div>
            <!-- Project co-creators -->
            <div class="form-group" v-if="coCreators.length > 0">
              <label class="sr-only">Add Co-creators</label>
              <v-select
                multiple
                class="project-select"
                v-model="coCreatorsValue"
                :options="coCreators"
                label="name"
                placeholder="Add Co-creators"
              >
                <template v-slot:selected-option="member">
                  <div class="option-img-name">
                    <img
                      v-if="member.avatarUri"
                      :src="'/storage/profiles/profilePictureImage/' + member.id + '/' + member.avatarUri"
                    />
                    <img v-else src="/images/user8-128x128.png" />
                    <span class="name">{{member.creativeTitle}}</span>
                  </div>
                </template>
                <template v-slot:option="member">
                  <div class="option-img-name">
                    <img
                      v-if="member.avatarUri"
                      :src="'/storage/profiles/profilePictureImage/' + member.id + '/' + member.avatarUri"
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
          <!--Form ends-->
        </div>
      </div>
    </div>
    <ModalUploadImage
      v-on:images="getImages($event)"
      :imgSrcCrouped="avatarCropImg"
      :cropper="cropper"
      v-if="showModal"
      @close="showModal = false"
    ></ModalUploadImage>
    <ModalUploadImage
      v-on:bgImages="getBgImages($event)"
      :backgroundSrcCrouped="cropImg"
      :cropper="cropperBg"
      v-if="showModalBgModal"
      @close="showModalBgModal = false"
    ></ModalUploadImage>
    <!-- Action buttons -->
    <div class="row create-gray">
      <div class="col-sm-12 col-md-6 offset-md-3">
        <div class="action-buttons">
          <router-link :to="{ path: prevRoute}" class="btn cancel">
            <i class="fas fa-times"></i> Cancel
          </router-link>
          <button v-if="!projectId" type="button" class="btn save" @click="saveProject">Save</button>
          <button v-else type="button" class="btn save" @click="saveProject">Edit</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import "vue-select/dist/vue-select.css";
import VueCropper from "vue-cropperjs";
import { helpers } from "../../mixins/helpers";
import "vue-select/dist/vue-select.css";
import ModalUploadImage from "../../shared/modals/ModalUploadImage";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

class error {
  constructor() {
    this.error = {};
  }

  get(field) {
    if (this.error[field]) {
      return this.error[field][0];
    }
  }

  record(error) {
    this.error = error;
  }
}
export default {
  name: "Form",
  components: { ModalUploadImage, VueCropper },
  middleware: "auth",
  mixins: [helpers],
  data() {
    return {
      selectedMembers: [],
      connectedMembers: [],
      selectedProfile: [],
      profiles: [],
      selectedTags: [],
      tags: [],
      cropper: "cropper",
      cropperBg: "cropperBg",
      showModal: false,
      showModalBgModal: false,
      tagsValue: [],
      newTags: [],
      tagArray: [],
      coverImageUrl: "",
      submitted: false,
      error: new error(),
      imgSrc: "",
      cropImg: "",
      image: "",
      originalBackgroundUrl: "",
      saveProjects: {
        name: "",
        description: "",
        type: 5,
        coverImage: "",
        profile: "",
        avatarUri: ""
      },
      avatarImgSrc: "",
      avatarCropImg: "",
      coCreators: [],
      coCreatorsValue: [],
      prevRoute: "",
      editor: ClassicEditor,
      validation: false,
      validationMessages: "",
      projectId: "",
      editorConfig: {
        toolbar: [
          "heading",
          "|",
          "bold",
          "italic",
          "link",
          "bulletedList",
          "numberedList",
          "blockQuote"
        ],
        placeholder: "Write project description...",
        heading: {
          options: [
            {
              model: "paragraph",
              title: "Paragraph",
              class: "ck-heading_paragraph"
            },
            {
              model: "heading1",
              view: "h1",
              title: "Heading 1",
              class: "ck-heading_heading1"
            },
            {
              model: "heading2",
              view: "h2",
              title: "Heading 2",
              class: "ck-heading_heading2"
            },
            {
              model: "heading3",
              view: "h3",
              title: "Heading 3",
              class: "ck-heading_heading3"
            },
            {
              model: "heading4",
              view: "h4",
              title: "Heading 4",
              class: "ck-heading_heading4"
            }
          ]
        }
      }
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

  methods: {
    addTag(newTag) {
      newTag.forEach((value, key) => {
        if (value.id) {
          if (
            $.inArray(value.id, this.tagArray) === -1 &&
            value.id != undefined
          ) {
            this.tagArray.push(value.id);
          }
        } else {
          if (value.name != undefined) {
            if (!this.newTags.includes(value.name)) {
              this.newTags.push(value.name);
            }
          } else {
            if (!this.newTags.includes(value)) {
              this.newTags.push(value);
            }
          }
        }
      });
    },

    getType(e) {
      (this.validationMessages = ""), (this.saveProjects.type = e);
    },

    getTags() {
      axios
        .get(apiRoute + "/user/tags", this.$store.getters["auth/token"])
        .then(response => {
          let _this = this;
          _this.tags = response.data.data;
        })
        .catch(error => {});
    },

    getCoCreators() {
      axios
        .get(apiRoute + "/user/co-creators", this.$store.getters["auth/token"])
        .then(response => {
          let _this = this;
          _this.coCreators = response.data.data;
        })
        .catch(error => {});
    },

    getProfiles() {
      axios
        .get(apiRoute + "/user/profiles", this.$store.getters["auth/token"])
        .then(response => {
          let _this = this;
          _this.profiles = response.data.data;
        })
        .catch(error => {});
    },

    getImages($event) {
      this.saveProjects.avatarUri = $event.profileImage;
      this.avatarCropImg = $event.cropImg;
    },

    getBgImages($event) {
      this.saveProjects.coverImage = $event.backgroundImage;
      this.cropImg = $event.croupBackgroundUrl;
      this.originalBackgroundUrl = $event.backgroundImage;
    },

    saveProject() {
      this.submitted = true;
      if (this.saveProjects.type == 5) {
        this.validation = false;
        this.validationMessages = "The project type filed is required";
      } else {
        this.validation = true;
      }

      this.$validator.validate().then(valid => {
        if (valid && this.validation) {
          this.tagsValue.forEach((value, key) => {
            if (
              $.inArray(value.id, this.tagArray) === -1 &&
              value.id != undefined &&
              !isNaN(value.id)
            ) {
              this.tagArray.push(value.id);
            }
          });

          if (this.projectId == undefined) {
            let formData = new FormData();
            formData.append("name", this.saveProjects.name);
            formData.append("isPublished", 1);
            formData.append("description", this.saveProjects.description);
            formData.append("type", this.saveProjects.type);
            formData.append("backgroundUri", this.saveProjects.coverImage);
            formData.append("avatarUri", this.saveProjects.avatarUri);
            formData.append(
              "originalBackgroundUrl",
              this.originalBackgroundUrl
            );
            formData.append("tags", JSON.stringify(this.tagArray));
            formData.append("newTags", JSON.stringify(this.newTags));
            formData.append(
              "coCreatorsValue",
              JSON.stringify(this.coCreatorsValue)
            );
            axios
              .post(
                apiRoute +
                  "/user/profiles/" +
                  this.saveProjects.profile.id +
                  "/projects",
                formData,
                this.$store.getters["auth/token"]
              )
              .then(response => {
                let _this = this;
                _this.projects = response.data.data;
                const profileId = response.data.data.profileId;
                const projectId = response.data.data.id;
                this.$router.push({
                  path: "/project-detail/project/" + projectId
                });
              })
              .catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error);
              });
          } else {
            let _this = this;
            if (this.tagsValue) {
              this.tagsValue.forEach((value, key) => {
                if (
                  $.inArray(value.id, this.tagArray) === -1 &&
                  value.id != undefined
                ) {
                  this.tagArray.push(value.id);
                }
              });
            } else {
              this.tagArray = [];
            }

            let formData = new FormData();
            formData.append("name", this.saveProjects.name);
            formData.append("isPublished", 1);
            formData.append("description", this.saveProjects.description);
            formData.append("type", this.saveProjects.type);
            formData.append("backgroundUri", this.saveProjects.coverImage);
            formData.append("avatarUri", this.saveProjects.avatarUri);
            formData.append(
              "originalBackgroundUri",
              this.originalBackgroundUrl
            );
            formData.append("imageUri", this.saveProjects.imageUri);
            formData.append("text", this.saveProjects.text);
            formData.append("link", this.saveProjects.link);
            formData.append("oldProfileId", this.oldProfileId);
            formData.append(
              "linkDescription",
              this.saveProjects.linkDescription
            );
            formData.append("tags", JSON.stringify(this.tagArray));
            formData.append("newTags", JSON.stringify(this.newTags));
            formData.append("textIds", JSON.stringify(this.textIds));
            formData.append(
              "updateTextIds",
              JSON.stringify(this.updateTextIds)
            );
            formData.append("updateLinks", JSON.stringify(this.updateLinks));
            formData.append(
              "coCreatorsValue",
              JSON.stringify(this.coCreatorsValue)
            );

            axios
              .post(
                apiRoute +
                  "/user/profiles/" +
                  this.saveProjects.profile.id +
                  "/projects/" +
                  this.saveProjects.id +
                  "/update",
                formData,
                this.$store.getters["auth/token"]
              )
              .then(response => {
                const profileId = response.data.data.profileId;
                const projectId = response.data.data.id;
                this.$router.push({
                  path: "/project-detail/project/" + projectId
                });
              })
              .catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error);
              });
          }
        }
      });
    },

    getProjectCoCreators() {
      axios
        .get(
          apiRoute + "/user/project/" + this.projectId + "/co-creators",
          this.$store.getters["auth/token"]
        )
        .then(response => {
          let _this = this;
          _this.coCreatorsValue = response.data.data;
        })
        .catch(error => {});
    },

    getProjects() {
      axios
        .get(
          apiRoute +
            "/user/projects/" +
            this.projectId,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          let _this = this;
          _this.saveProjects = response.data.data;
          _this.tagsValue = response.data.data.tags;
          if (response.data.data.backgroundUri) {
            _this.cropImg =
              "/storage/projects/projectBackgroundImg/" +
              response.data.data.id +
              "/" +
              response.data.data.backgroundUri;
            _this.imgSrc =
              "/storage/projects/projectBackgroundImg/" +
              response.data.data.id +
              "/" +
              response.data.data.backgroundUri;
          }

          if (response.data.data.avatarUri) {
            _this.avatarCropImg =
              "/storage/projects/projectAvatar/" +
              response.data.data.id +
              "/" +
              response.data.data.avatarUri;
            _this.avatarCropImg =
              "/storage/projects/projectAvatar/" +
              response.data.data.id +
              "/" +
              response.data.data.avatarUri;
          }

          if (response.data.data.imageUri) {
            _this.imgUrl =
              "/storage/projects/projectImage/" +
              response.data.data.id +
              "/" +
              response.data.data.imageUri;
          }
        })
        .catch(error => {});
    }
  },

  created() {
    this.projectId = this.$route.params.projectId;
    // this.profileId = this.$route.params.profileId;
    // this.oldProfileId = this.$route.params.profileId;
  },

  mounted() {
    bus.$emit("overlay", 1);
    this.getTags();
    this.getProfiles();
    this.getCoCreators();

    if (this.projectId) {
      this.getProjects();
      this.getProjectCoCreators();
    }
  }
};
</script>

<style>
.ck-editor__editable {
  background-color: white !important;
  border-bottom: 1px solid #c4c4c4 !important;
  color: #000 !important;
}
</style>
<style lang="scss">
.tags-select {
  .vs__dropdown-toggle {
    font-size: 1.2rem;
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
}
.project-select {
  .vs__dropdown-toggle {
    font-size: 1.2rem;
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
  .close-button {
    color: #474747;
    font-size: 1.6rem;
  }
}
.project-name-avatar {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  .project-avatar {
    position: relative;
    border-radius: 50%;
    width: 110px;
    height: 110px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 15px;
    background-color: #fff;
    border: 1px solid #474747;
    cursor: pointer;
    i.fa-user {
      color: #474747;
      font-size: 2.5rem;
    }
    i.fa-plus {
      position: absolute;
      top: 85px;
      right: 5px;
      color: #474747;
      background: #fff;
      font-size: 1rem;
      border-radius: 50%;
      border: 1px solid #474747;
      display: inline-block;
      padding: 0.4rem 0.4rem;
      z-index: 1;
      @media (max-width: 500px) {
        padding: 0.4rem 0.4rem;
        top: 75px;
        right: 10px;
      }
    }
    @media (max-width: 500px) {
      width: 90px;
      height: 90px;
    }
  }
  .project-name {
    width: 70%;

    @media (max-width: 500px) {
      width: 60%;
    }
  }
}

.add-project-cover {
  position: relative;
  display: block;
  cursor: pointer;
  border: 1px solid #474747;
  border-radius: 0.25rem;
  padding: 20px 0;
  width: 100%;
  text-align: center;
  color: #474747;
  i.fa-camera {
    color: #474747;
    font-size: 2.5rem;
  }
}
.select-project-type {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  .label {
    font-size: 1.2rem;
    color: #474747;
  }
  .types {
    max-width: 300px;
    margin: 0 auto;
    display: flex;
    justify-content: center;
    a {
      text-decoration: none;
      &.disabled {
        cursor: default;
      }
      .circle {
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-weight: bold;
        font-size: 1.4rem;
        font-family: EncodeSansBold;
        display: flex;
        justify-content: center;
        align-items: center;
        &.w {
          color: #01ffc3;
          border: 1px solid #01ffc3;
        }
        &.a {
          margin: 0 20px;
          color: #ff90fc;
          border: 1px solid #ff90fc;
        }
        &.v {
          color: #01aeff;
          border: 1px solid #01aeff;
        }
      }
      &.active {
        .circle {
          &.w {
            color: #fff;
            background: #01ffc3;
          }
          &.a {
            color: #fff;
            background: #ff90fc;
          }
          &.v {
            color: #fff;
            background: #01aeff;
          }
        }
      }
    }
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

.profile-picture-image {
  position: absolute;
  cursor: pointer;
  display: inline-block;
  border-radius: 50%;
  padding: 0.2em 0.3em;
  color: #ccc;
  font-size: 5rem;
  width: 160px;
  height: 144px;
  top: -17px;
  left: -25px;
}

@media (max-width: 500px) {
  .profile-picture-image {
    top: -19px;
    left: -35px;
    width: 150px;
    height: 128px;
  }
}

.background-image {
  max-width: 100%;
  max-height: 155px;
}
</style>




