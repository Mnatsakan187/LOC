<template>
  <div class="container-fluid create">
    <div class="row create-white">
      <div class="col-sm-12 col-md-8 col-xl-6 offset-md-2 offset-xl-3 mt-5 mb-5 limit-form-width">
        <!--Header-->
        <div class="header">
          <div v-if="!postId" class="heading">New Post</div>
          <div v-else class="heading">Edit Post</div>
          <router-link :to="{ path: prevRoute}" class="btn close-button">
            <i class="fas fa-times"></i>
          </router-link>
        </div>
        <div class="content mt-5">
          <!--Form begins-->
          <form  onsubmit="return false">
            <!-- Select profile -->
            <div class="form-group mt-4">
              <label class="sr-only">Select profile</label>
              <v-select
                class="icon-select"
                v-validate="{'required': !post.project}"
                v-model="post.profile"
                :options="profiles"
                label="creativeTitle"
                placeholder="Assign to profile"
                name="profile" id="profile"
              >
                <template v-slot:selected-option="profile">
                  <div class="option-img-name">
                    <img v-if="profile.avatarUri" :src="'/storage/profiles/profilePictureImage/' + profile.id + '/' + profile.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    <!--<span class="name">{{profile.user.firstName}} {{profile.user.lastName}}</span>-->
                    <span class="title">{{profile.creativeTitle}}</span>
                  </div>
                </template>
                <template v-slot:option="profile">
                  <div class="option-img-name">
                    <img v-if="profile.avatarUri" :src="'/storage/profiles/profilePictureImage/' + profile.id + '/' + profile.avatarUri"/>
                    <img v-else src="/images/user8-128x128.png"/>
                    <div class="name-title">
                      <span class="name">{{profile.user.firstName}} {{profile.user.lastName}}</span>
                      <span class="title">{{profile.creativeTitle}}</span>
                    </div>
                  </div>
                </template>
              </v-select>
              <span class="invalid-feedback" v-if="submitted && errors.has('profile')">{{ errors.first('profile') }}</span>
              <span class="invalid-feedback" v-text="error.get('profile')"></span>
            </div>

            <!-- Select project -->
            <div class="form-group mt-4">
              <label class="sr-only">Select project</label>
              <v-select
                class="icon-select"
                v-validate="{'required': !post.profile}"
                v-model="post.project"
                name="project"
                :options="projects"
                label="name"
                placeholder="Assign to project"
              >
                <template v-slot:selected-option="project">
                  <div class="option-img-name">
                    <img v-if="project.avatarUri" :src="'/storage/projects/projectAvatar/' + project.id + '/' + project.avatarUri"/>
                    <img  v-else src="/images/user8-128x128.png"/>
                    <span class="name">{{project.name}}</span>
                  </div>
                </template>
                <template v-slot:option="project">
                  <div class="option-img-name">
                    <img v-if="project.avatarUri" :src="'/storage/projects/projectAvatar/' + project.id + '/' + project.avatarUri"/>
                    <img  v-else src="/images/user8-128x128.png"/>
                    <div class="name-title">
                      <span class="name">{{project.name}}</span>
                    </div>
                  </div>
                </template>
              </v-select>
              <span class="invalid-feedback" v-if="submitted && errors.has('project')">{{ errors.first('project') }}</span>
              <span class="invalid-feedback" v-text="error.get('project')"></span>
            </div>
            <!-- Select group -->
            <div class="form-group mt-4">
              <label class="sr-only">Select group</label>
              <v-select
                class="group-select"
                v-model="post.group"
                name="group"
                :options="groups"
                label="name"
                placeholder="Assign to group"
              ></v-select>
            </div>

            <!--Post message-->
            <div class="form-group mt-4">
              <label class="sr-only" for="postMessage">Post Message</label>
              <ckeditor id="createPost" placeholder="Write your post..."
                        v-validate="'required|max:1000'" name="summary" v-model="post.summary" :editor="editor"   :config="editorConfig"></ckeditor>
              <span class="invalid-feedback" v-if="submitted && errors.has('summary')">{{ errors.first('summary') }}</span>
              <span class="invalid-feedback" v-text="error.get('summary')"></span>
            </div>
            <!-- Post external link -->
            <div class="add-post-links mt-4">
              <div
                class="description pt-2 pb-2"
              >Add an external link. You can copy and paste your link here:</div>
              <div class="url-list" v-if="urlList.length >0">
                <ul>
                  <li v-for="item in urlList">
                    <span class="link-url">{{item.url}}</span>
                    <i class="far fa-trash-alt"  @click="removeLink(item.id)"></i>
                  </li>
                </ul>
              </div>
              <div class="url-add mt-3">
                <div class="form-group">
                  <label class="sr-only" for="externalLink">External link</label>
                  <input
                    type="text"
                    class="form-control"
                    id="externalLink"
                    placeholder="External link"
                    v-model="link"
                    @input="deleteError()"
                  />
                </div>

                <button class="btn" @click="addUrl">
                  <i class="fas fa-plus"></i> Add
                </button>
              </div>
              <span v-if="errorMessages" class="invalid-feedback">{{errorMessages}}</span>
            </div>

            <!-- Post images -->

            <div v-if="!postId" class="add-post-images mt-4 pt-3">
              <div class="post-images">
                <div class="post-image" v-for="image in images">
                  <img :src="image.image" />
                  <i class="far fa-trash-alt"  @click="removeImage(image.id)"></i>
                </div>
              </div>
              <button class="btn add-image" @click="addImage">
                <i class="fas fa-plus"></i> Add Image
              </button>
            </div>


            <!-- Post images -->
            <div v-else class="add-post-images mt-4 pt-3">
              <div class="post-images">
                <div class="post-image" v-for="image in images">
                  <img v-if="image.fieldName == 'image'" :src="'/storage/posts/mediaPostImage/media/' + image.id + '/' + image.url"/>
                  <img v-else :src="image.image"/>
                  <i  class="far fa-trash-alt"  @click="removeImage(image.id, 'image')"></i>
                </div>
              </div>
              <button class="btn add-image" @click="addImage">
                <i class="fas fa-plus"></i> Add Image
              </button>
            </div>
          </form>
          <!--Form ends-->
        </div>
      </div>
    </div>
    <!-- Action buttons -->
    <div class="row create-gray">
      <div class="col-sm-12 col-md-6 offset-md-3">
        <div class="action-buttons">
          <router-link :to="{ path: prevRoute}" class="btn cancel">
            <i class="fas fa-times"></i> Cancel
          </router-link>
          <button v-if="!postId" type="button" class="btn save" @click="validationAddPost">Save</button>
          <button v-else type="button" class="btn save" @click="validationAddPost">Edit</button>
        </div>
      </div>
    </div>
    <ModalConfirmation :type="type"  v-if="showConfModal" @close="showConfModal = false">
      <div slot="body">Are you sure, you want to delete this item?</div>
    </ModalConfirmation>

    <ModalUploadImage v-on:images="getImages($event)" :imgSrcCrouped="cropImg" :cropper="cropper"  v-if="showModal" @close="showModal = false"></ModalUploadImage>
  </div>
</template>

<script>
  import ModalUploadImage from '../../shared/modals/ModalUploadImage'
  import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

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
  import ModalConfirmation from "../../shared/modals/ModalConfirmation.vue";
  import 'vue-select/dist/vue-select.css';
  export default {
    name: "Form",
    data() {
      return {
        selectedProfile: [],
        profiles: [],
        selectedProject: [],
        projects: [],
        selectedGroup: [],
        groups: [],
        urlList: [],
        showConfModal: false,
        link: '',
        users: [],
        submitted:false,
        prevRoute: '',
        error: new error(),
        messages: false,
        post: {
          description: '',
          summary: '',
          groupId: '',
          profileId: '',
          imageUri: '',
          projectId: '',
          group: '',
          project: '',
          profile: ''
        },
        imgSrc: '',
        cropImg: '',
        image: '',
        user: {},
        assignToProject: false,
        errorMessages: '',
        i: 1,
        linkId: '',
        type: '',
        images:[],
        imagesUpload:[],
        cropper: 'post',
        cropperBg: 'cropperBg',
        showModal: false,
        imageI: 1,
        imageId: '',
        editor: ClassicEditor,
        urlDelete: [],
        editorConfig: {
          toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
          heading: {
            options: [
              { model: 'paragraph',            title: 'Paragraph', class: 'ck-heading_paragraph' },
              { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
              { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
              { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
              { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
            ]
          }
        },

        createdBy: '',
        selectedMembers: [],
        profileOrProjectRequiredMsg: 'Please, assign to either profile or  project'
      };
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

    components: {
      ModalUploadImage,
      ModalConfirmation
    },

    methods: {
      addUrl() {
        if(this.link.includes('instagram') || this.link.includes('youtube')){
          this.urlList.push({'id': this.i, url:this.link});
          this.link = '';
          this.i++
          this.errorMessages = '';
        }else{
          this.errorMessages  = 'The url must be youtube or instagram link';
        }

      },

      deleteError(){
        this.errorMessages = '';
      },

      getImages($event){
        console.log($event, 545454)
        this.image            =   $event.profileImage;
        this.cropImg          =   $event.cropImg;
        this.createdBy        =   $event.createdBy;
        this.selectedMembers  =   $event.selectedMembers;
        this.images.push({'id':this.imageI, 'image': $event.cropImg});
        this.imagesUpload.push({'id':this.imageI, 'image': $event.profileImage, 'createdByImage' : $event.createdBy});
        this.imageI++
      },

      removeImage(id){
        this.showConfModal = true
        this.type = 'image';
        this.imageId = id;
      },

      removeLink(id){
        this.showConfModal = true
        this.type = 'link';
        this.linkId = id;
      },

      addImage(){
        this.cropImg = ''
        this.showModal = true

      },

      getItemIdOrEmptyString(item) {
        return item ? item.id : ''
      },

      validationAddPost() {
        this.submitted = true;
        let _this = this;
        this.$validator.validate().then(valid => {
          if (valid) {
            if(this.postId == undefined) {
              let formData = new FormData();
              formData.append('description',       this.post.description);
              formData.append('summary',           this.post.summary);
              formData.append('groupId',           this.getItemIdOrEmptyString(this.post.group));
              formData.append('imageUri',          this.post.imageUri);
              formData.append('profileId',         this.getItemIdOrEmptyString(this.post.profile));
              formData.append('projectId',         this.getItemIdOrEmptyString(this.post.project));
              formData.append('urlList',           JSON.stringify(this.urlList));
              axios.post(apiRoute + '/user/posts', formData, this.$store.getters['auth/token']).then(response => {
                this.imagesUpload.forEach((item) => {
                  let imageFormData = new FormData();
                  imageFormData.append('displayName', 'image');
                  imageFormData.append('fieldName', 'image');
                  imageFormData.append('uri', 'image');
                  imageFormData.append('mediaType', 0);
                  imageFormData.append('imageUris', item.image);
                  imageFormData.append('createdByImage', item.createdByImage);

                  axios.post(apiRoute + '/user/posts/'+response.data.data.id+'/media', imageFormData, this.$store.getters['auth/token']).then(response => {

                  }).catch(error => {

                  })

                });

                this.urlList.forEach((item) => {
                  let imageFormData = new FormData();
                  imageFormData.append('displayName', 'link');
                  imageFormData.append('fieldName', 'link');
                  imageFormData.append('uri', item.url);
                  imageFormData.append('mediaType', 5);
                  axios.post(apiRoute + '/user/posts/'+response.data.data.id+'/media', imageFormData, this.$store.getters['auth/token']).then(response => {

                  }).catch(error => {

                  })

                });

                setTimeout(function() {
                  _this.$router.push({path: '/post/post-detail/'+response.data.data.id})
                }, 500);

                _this.messages = true
              }).catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error)
              });


            }else{
              let formData = new FormData();
              formData.append('description',      this.post.description);
              formData.append('summary',          this.post.summary);
              formData.append('images',           JSON.stringify(this.images));
              formData.append('urlDelete',        JSON.stringify(this.urlDelete));
              // formData.append('createdByArray',   JSON.stringify(this.selectedMembers));

              formData.append('groupId',  this.getItemIdOrEmptyString(this.post.group));

              formData.append('imageUri', this.post.imageUri);

              formData.append('projectId', this.getItemIdOrEmptyString(this.post.project))

              formData.append('profileId', this.getItemIdOrEmptyString(this.post.project))

              axios.post(apiRoute + '/user/posts/'+this.post.id+'/update', formData, this.$store.getters['auth/token']).then(response => {
                this.urlList.forEach((item, key) => {
                  let imageFormData = new FormData();
                  imageFormData.append('displayName', 'link');
                  imageFormData.append('fieldName', 'link');
                  imageFormData.append('uri', item.url);
                  imageFormData.append('mediaType', 5);
                  imageFormData.append('old', item.old);

                  axios.post(apiRoute + '/user/posts/'+response.data.data.id+'/media/update', imageFormData, this.$store.getters['auth/token']).then(response => {

                  }).catch(error => {

                  })

                });

                this.imagesUpload.forEach((item, key) => {
                  let imageFormData = new FormData();
                  imageFormData.append('displayName', 'image');
                  imageFormData.append('fieldName', 'image');
                  imageFormData.append('uri', 'image');
                  imageFormData.append('mediaType', 0);
                  imageFormData.append('imageUris', item.image);
                  imageFormData.append('old', item.old);
                  imageFormData.append('createdByImage', item.createdByImage);

                  axios.post(apiRoute + '/user/posts/'+response.data.data.id+'/media/update', imageFormData, this.$store.getters['auth/token']).then(response => {

                  }).catch(error => {

                  })

                });

                console.log(this.post)
                debugger
                // this.$router.push({path: '/profile-detail/'+_this.post.profileId})
                _this.messages = true
              }).catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error)
              });
            }


          }
        });
      },


      getUser(){
        let _this = this;
        axios.get(apiRoute + '/user', this.$store.getters['auth/token']).then(response => {
          _this.user = response.data.data;

          axios.get(apiRoute + '/user/'+response.data.data.id+'/projects', this.$store.getters['auth/token']).then(response => {
            _this.projects = response.data.data;
          }).catch(error => {
            this.showLoader = false
          })

        }).catch(error => {

        })
      },


      getProfiles() {
        axios.get(apiRoute + '/user/profiles', this.$store.getters['auth/token']).then(response => {
          let _this = this;
          _this.profiles = response.data.data;
        }).catch(error => {

        })
      },

      getGroups() {
        axios.get(apiRoute + '/user/groups', this.$store.getters['auth/token']).then(response => {
          let _this = this;
          _this.groups = response.data.data;
        }).catch(error => {

        })
      },

      setImage(e) {
        const file = e.target.files[0];
        if (!file.type.includes("image/")) {
          alert("Please select an image file");
          return;
        }

        if (typeof FileReader === "function") {
          const reader = new FileReader();

          reader.onload = event => {
            this.imgSrc = event.target.result;
            if(this.$refs.cropper != undefined) {
              this.$refs.cropper.replace(event.target.result);
            }
          };
          reader.readAsDataURL(file);
        } else {
          alert("Sorry, FileReader API not supported");
        }
      },

      cropImage() {
        this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
        $("#postAvatarModal").modal('hide');
        this.post.imageUri = this.dataURLtoFile(this.cropImg , 'file');
      },


      getUser(){
        let _this = this;
        axios.get(apiRoute + '/user', this.$store.getters['auth/token']).then(response => {
          _this.user = response.data.data;

          axios.get(apiRoute + '/user/'+response.data.data.id+'/projects', this.$store.getters['auth/token']).then(response => {
            _this.projects = response.data.data;
          }).catch(error => {
            this.showLoader = false
          })

        }).catch(error => {

        })
      },


      getPosts() {
        let _this = this;
        axios.get(apiRoute + '/user/posts/' + this.postId, this.$store.getters['auth/token']).then(response => {
          _this.post = response.data.data;

          response.data.data.images.forEach((item) => {
            this.images.push({'id': item.id, url:item.uri, fieldName: 'image', old:1})
          });

          response.data.data.links.forEach((item) => {
            this.urlList.push({'id': item.id, url:item.uri, old:1})
            this.urlDelete.push({'id': item.id, url:item.uri})
          });


          if(response.data.data.imageUri){
            _this.cropImg = '/storage/posts/'+response.data.data.id +'/'+response.data.data.imageUri;
            _this.imgSrc = '/storage/posts/'+response.data.data.id +'/'+response.data.data.imageUri;
          }

          if(response.data.data.projectId){
            this.assignToProject = true
          }


          if(response.data.data.projectId){
            axios.get(apiRoute+ '/user/projects/'+response.data.data.projectId).then(response => {
              _this.post.project = response.data.data;
            }).catch(function (error) {

            });
          }


          if(response.data.data.groupId){
            axios.get(apiRoute + '/user/groups/'+response.data.data.groupId,  this.$store.getters['auth/token']).then(response => {
              _this.post.group = response.data.data;
            }).catch(error => {

            });
          }


        }).catch(error => {

        })
      },

      prepareValidatorErrorMessages() {
        const dict =  {
          custom: {
            profile: {
              required: () => this.profileOrProjectRequiredMsg
            },
            project: {
              required: () => this.profileOrProjectRequiredMsg
            }
          }
        }

        this.$validator.localize('en', dict)
      }
    },

    created() {
      this.prepareValidatorErrorMessages()
      this.postId = this.$route.params.postId
      // this.profileId = this.$route.params.profileId
      this.getUser();


      bus.$on('deleteLink', (data) => {
        if(data == 1){
          this.urlList.forEach((value, key) => {
            if(value.id == this.linkId){
              this.$delete(this.urlList, key)
            }
          })

          this.urlDelete.forEach((value, key) => {
            if(value.id == this.linkId){
              this.$delete(this.urlDelete, key)
            }
          })

          this.showConfModal = false;
        }
      })


      bus.$on('deleteImage', (data) => {
        if(data == 1){
          this.images.forEach((value, key) => {
            if(value.id == this.imageId){
              this.$delete(this.images, key)
              this.$delete(this.imagesUpload, key)
            }
          })
          this.showConfModal = false;
        }
      })


    },


    mounted() {
      bus.$emit('overlay', 1)
      this.getProfiles();
      this.getGroups();

      if(this.postId){
        this.getPosts();
        this.getUser();
      }
    }
  };
</script>
<style lang="scss">
  .group-select {
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
  .icon-select {
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
  .limit-form-width {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
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

  .add-post-links {
    border-top: 1px solid #bababa;
    color: #474747;
    font-size: 1.1rem;
    .url-list ul {
      list-style: none;
      margin: 0;
      padding: 0;
      li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
        .link-url {
          color: #60d7d6;
          font-size: 1.1rem;
          text-decoration: underline;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        i {
          color: red;
          cursor: pointer;
        }
      }
    }
    .url-add {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      .form-group {
        margin: 0;
        width: 60%;
      }
      .btn {
        color: #60d7d6;
        font-family: EncodeSansSemiBold;
        font-size: 1.2rem;
      }
    }
  }

  .add-post-images {
    border-top: 1px solid #bababa;
    .post-images {
      display: flex;
      justify-content: flex-start;
      flex-flow: row wrap;
      .post-image {
        position: relative;
        width: 23%;
        margin-left: 1.6%;
        margin-bottom: 10px;
        img {
          object-fit: cover;
          width: 100%;
          height: 70px;
        }
        i {
          position: absolute;
          top: 5px;
          right: 5px;
          color: red;
        }
      }
    }
    .add-image {
      color: #474747;
      background: #60d7d6;
      transition: 0.3s;
      border-radius: 20px;
      font-size: 1.2rem;
      padding: 8px 15px;
      margin-top: 10px;
      i {
        color: #fff;
        margin-right: 5px;
      }
      &:hover {
        background: #81dedd;
        transition: 0.3s;
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
</style>
<style>
  .ck-editor__editable {
    background-color: white !important;
    border-bottom: 1px solid #c4c4c4 !important;
    color: #000 !important;
  }
</style>

