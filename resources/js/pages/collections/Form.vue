<template>
  <div class="container-fluid create">
    <div class="row create-white">
      <div
        class="col-sm-12 col-md-8 col-lg-6 col-xl-4 offset-md-2 offset-lg-3 offset-xl-4 mt-5 mb-5"
      >
        <div class="header">
          <div class="heading" v-if="!collectionId">New collection</div>
          <div class="heading" v-else>Edit collection</div>
          <router-link class="btn close-button" :to="{ path: prevRoute}">
            <i class="fas fa-times"></i>
          </router-link>
        </div>
        <div class="content mt-5">
          <!--Collection Form Begins-->
          <form class="register-creator-step-a" onsubmit="return false" novalidate>
            <!-- Enter Collection Name -->
            <div class="form-group">
              <label class="sr-only" for="collectionName">Collection Name</label>
              <input
                v-on:input="handler"
                v-model="collection.name"
                id="collectionName"
                name="name"
                type="text"
                class="form-control"
                placeholder="Collection name"
                v-validate="'required'"
              />
              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('name')"
              >{{ errors.first('name') }}</span>
              <span class="invalid-feedback" v-if="collection.name" v-text="error.get('name')"></span>
            </div>
            <!--Collection Cover block-->
            <div class="add-collection-cover mt-4" @click="showModal = true">
              <h5 v-if="!cropImg">Add collection cover</h5>
              <p v-if="!cropImg">This image will be the cover of your collection tile.</p>
              <i class="fas fa-camera" v-if="!cropImg"></i>
              <div v-if="cropImg" class="image-cover">
                <img :src="cropImg" class="image" />
              </div>
            </div>
          </form>
          <!--Collection Form Ends-->
        </div>
      </div>
    </div>
    <ModalUploadImage
      v-on:images="getImages($event)"
      :imgSrcCrouped="cropImg"
      :cropper="cropper"
      v-if="showModal"
      @close="showModal = false"
    ></ModalUploadImage>
    <div class="row create-gray">
      <div class="col-sm-12 col-md-6 offset-md-3">
        <div class="action-buttons">
          <router-link class="btn cancel" :to="{ path: prevRoute}">
            <i class="fas fa-times"></i> Cancel
          </router-link>
          <button
            v-if="!collectionId"
            type="button"
            @click="validationAddPost"
            class="btn save"
          >Save</button>
          <button v-else type="button" @click="validationAddPost" class="btn save">Edit</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ModalUploadImage from "../../shared/modals/ModalUploadImage";
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

import { helpers } from "../../mixins/helpers";
export default {
  name: "Form",
  components: { ModalUploadImage },
  middleware: "auth",
  mixins: [helpers],
  data: () => ({
    users: [],
    submitted: false,
    prevRoute: "",
    error: new error(),
    profiles: {},
    collection: {
      name: "",
      backgroundUri: "",
      profileId: ""
    },
    imgSrc: "",
    cropImg: "",
    image: "",
    check: "",
    showModal: false,
    cropper: "cropper",
    showFooterOverlay: false,
    collectionId: ""
  }),

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
    validationAddPost() {
      this.submitted = true;
      let _this = this;
      $(".invalid-feedback").show();
      this.$validator.validate().then(valid => {
        if (valid) {
          if (this.collectionId == undefined) {
            let formData = new FormData();
            formData.append("name", this.collection.name);
            formData.append("backgroundUri", this.collection.backgroundUri);
            axios
              .post(
                apiRoute + "/user/profiles/1/collections",
                formData,
                this.$store.getters["auth/token"]
              )
              .then(response => {
                this.$router.push({ path: _this.prevRoute });
              })
              .catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error);
              });
          } else {
            let formData = new FormData();
            formData.append("name", this.collection.name);
            formData.append("backgroundUri", this.collection.backgroundUri);
            axios
              .post(
                apiRoute +
                  "/user/profiles/1/collections/" +
                  this.collectionId +
                  "/update",
                formData,
                this.$store.getters["auth/token"]
              )
              .then(response => {
                this.$router.push({ path: _this.prevRoute });
              })
              .catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error);
              });
          }
        }
      });
    },

    handler() {
      $(".invalid-feedback").hide();
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
      this.collection.backgroundUri = $event.profileImage;
      this.cropImg = $event.cropImg;
    },

    getCollection() {
      let _this = this;
      axios
        .get(
          apiRoute + "/user/profiles/1/collections/" + this.collectionId,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          _this.collection = response.data.data;
          if (response.data.data.backgroundUri) {
            _this.cropImg =
              "/storage/collections/" +
              response.data.data.id +
              "/" +
              response.data.data.backgroundUri;
            _this.imgSrc =
              "/storage/collections/" +
              response.data.data.id +
              "/" +
              response.data.data.backgroundUri;
          }
        })
        .catch(error => {});
    }
  },

  created() {
    this.collectionId = this.$route.params.collectionId;
  },

  mounted() {
    bus.$emit("overlay", 1);
    this.getProfiles();

    if (this.collectionId) {
      this.getCollection();
    }
  }
};
</script>

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
.invalid-feedback {
  color: #ff90fc;
  margin-top: 10px;
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

.add-collection-cover {
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

.image {
  max-width: 100%;
  max-height: 155px;
}
</style>
