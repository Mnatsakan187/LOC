<template>
  <!-- Main content-->
  <div class="container-fluid create">
    <div class="row create-white">
      <div class="col-sm-12 col-md-8 col-xl-6 offset-md-2 offset-xl-3 mt-5 mb-5 limit-form-width">
        <!--Header-->
        <div class="header">
          <div class="heading" v-if="!pollId">New Poll</div>
          <div class="heading" v-else>Edit Poll</div>
          <router-link :to="{ path: prevRoute}" class="btn close-button">
            <i class="fas fa-times"></i>
          </router-link>
        </div>
        <div class="content mt-5">
          <!--Form begins-->
          <form onsubmit="return false">
            <!-- Project name -->
            <div class="project-name">
              <div class="form-group">
                <label class="sr-only" for="name">Project Name</label>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Poll name"
                  v-validate="'required|max:100'"  v-model="poll.name" id="name" name="name"
                />
                <span class="invalid-feedback" v-if="submitted && errors.has('name')">{{ errors.first('name') }}</span>
                <span class="invalid-feedback"  v-text="error.get('name')"></span>
              </div>
            </div>


            <!-- Select profile -->
            <div class="form-group mt-4">
              <label class="sr-only">Select profile</label>
              <v-select
                class="icon-select"
                v-model="poll.profile"
                name="profile" id="profile"
                :options="profiles"
                @input="deleteMessages"
                label="creativeTitle"
                placeholder="Assign to profile">
                <template v-slot:selected-option="profile">
                  <div class="option-img-name">
                    <img v-if="profile.avatarUri" :src="'/storage/profiles/profilePictureImage/' + profile.id + '/' + profile.avatarUri" />
                    <img v-else src="/images/user8-128x128.png"/>
                    <span class="name">{{profile.creativeTitle}}</span>
                  </div>
                </template>
                <template v-slot:option="profile">
                  <div class="option-img-name">
                    <img v-if="profile.avatarUri" :src="'/storage/profiles/profilePictureImage/' + profile.id + '/' + profile.avatarUri" />
                    <img v-else src="/images/user8-128x128.png"/>
                    <div class="name-title">
                      <span class="name">{{profile.user.firstName}} {{profile.user.lastName}}</span>
                      <span class="title">{{profile.creativeTitle}}</span>
                    </div>
                  </div>
                </template>
              </v-select>
              <span class="invalid-feedback" v-if="submitted && errorMessage">{{ errorMessage }}</span>
            </div>


            <!-- Select project -->
            <div class="form-group mt-4">
              <label class="sr-only">Select project</label>
              <v-select
                class="icon-select"
                v-model="poll.projectId"
                :options="projects"
                @input="deleteMessages"
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
              <span class="invalid-feedback" v-if="submitted && errorMessage">{{ errorMessage }}</span>
            </div>

            <!--Poll question-->
            <div class="form-group mt-4">
              <label class="sr-only" for="pollQuestion">Poll Question</label>
              <textarea
                v-validate="'required|max:1000'"  v-model="poll.question"
                class="form-control"
                id="pollQuestion"
                rows="5"
                placeholder="Write your question..."
                name="question"
              ></textarea>
              <span class="invalid-feedback" v-if="submitted && errors.has('question')">{{ errors.first('question') }}</span>
            </div>
            <div class="answer-select">
              <button
                class="btn"
                :class="{ active: poll.answerType == 2 }"
                @click="getPollType(2)"
              >Set responses</button>
              <button
                class="btn"
                :class="{ active: poll.answerType == 1 }"
                @click="getPollType(1)"
              >Open answer</button>
            </div>

            <div v-if="!pollId">
              <!-- Poll set responses -->
              <div  class="poll-set-responses mt-4" v-if="poll.answerType == 2">
                <div class="description pt-2 pb-2">Set responses to your poll:</div>
                <div class="response-list">
                  <div v-for="(ans, key) in poll.answers" class="input-group mb-2 mt-1">
                    <input  v-validate="'required'"  v-model="ans.value"
                            type="text"
                            :name="'answer'+key"
                            class="form-control"
                            placeholder="Write your response here..."
                    />
                    <i class="far fa-trash-alt delete-icon" @click="deleteAnswer(key)"  v-on:click="showConfModal = true"></i>
                    <span class="invalid-feedback" v-if="submitted && errors.has('answer'+key )">The answer field is required.</span>
                  </div>
                  <button v-if="poll.answerType == 2"   @click="addFind" class="btn add-response">
                    <i class="fas fa-plus"></i> Add another option
                  </button>
                </div>
              </div>
            </div>

            <div v-else>
              <!-- Poll set responses -->
              <div  class="poll-set-responses mt-4" v-if="poll.answerType == 2">
                <div class="description pt-2 pb-2">Set responses to your poll:</div>
                <div class="response-list">
                  <div v-for="(ans, key) in poll.fillAnswers" class="input-group mb-2 mt-1">
                    <input  v-validate="'required'"  v-model="ans.value"
                            type="text"
                            :name="'answer'+key"
                            class="form-control"
                            placeholder="Write your response here..."
                    />
                    <i class="far fa-trash-alt delete-icon" @click="deleteAnswer(key)"  v-on:click="showConfModal = true"></i>
                    <span class="invalid-feedback" v-if="submitted && errors.has('answer'+key )">The answer field is required.</span>
                  </div>
                  <button v-if="poll.answerType == 2"   @click="addFind" class="btn add-response">
                    <i class="fas fa-plus"></i> Add another option
                  </button>
                </div>
              </div>
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
          <router-link  :to="{ path: prevRoute}" class="btn cancel">
            <i class="fas fa-times"></i> Cancel
          </router-link>
          <button v-if="!pollId"  @click="validationAddPoll"  type="button" class="btn save">Save</button>
          <button v-else  @click="validationAddPoll"  type="button" class="btn save">Edit</button>
        </div>
      </div>
    </div>
    <ModalConfirmation v-if="showConfModal" @close="showConfModal = false" :deleteId="answerKey" :type="type">
      <div slot="body">Are you sure, you want to delete this item?</div>
    </ModalConfirmation>
  </div>
</template>

<script>
  import 'vue-select/dist/vue-select.css';
  import ModalConfirmation from '../../shared/modals/ModalConfirmation'
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
    name: 'Form',
    components: { ModalConfirmation },
    middleware: 'auth',
    data: () => ({
      users: [],
      submitted:false,
      assignToProject:false,
      prevRoute: '',
      error: new error(),
      profiles: [],
      projects: [],
      user: {},
      showConfModal: false,
      poll: {
        name: '',
        question: '',
        answer: '',
        profileId: '',
        projectId: '',
        answerType: 2,
        fillAnswers:[],
        project:[],
        profile: [],
        answers: []
      },
      answerKey: '',
      type: 'answer',
      errorMessage: '',
      validation: false,
      pollId: '',
      profileId: '',

    }),

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
      validationAddPoll() {
        this.submitted = true;
        let _this = this;

        if(!this.poll.profile && !this.poll.projectId){
          debugger
          this.errorMessage = 'You must choose at least a profile or a project to continue'
          this.validation = false
        }else {
          this.validation = true
          this.errorMessage = ''
        }

        if(this.pollId == undefined){
          if(Array.isArray(this.poll.profile)){
            if(!this.poll.profile.length && !this.poll.projectId){
              this.errorMessage = 'You must choose at least a profile or a project to continue'
              this.validation = false
            }else {
              this.validation = true
              this.errorMessage = ''
            }
          }else{
            if(!this.poll.profile && !this.poll.projectId){
              this.errorMessage = 'You must choose at least a profile or a project to continue'
              this.validation = false
            }else {
              this.validation = true
              this.errorMessage = ''
            }
          }

        }

        this.$validator.validate().then(valid => {
          if (valid && this.validation) {
            if(this.pollId == undefined){
              axios.post(apiRoute + '/user/polls', this.poll,  this.$store.getters['auth/token']).then(response => {
                this.$router.push({path: '/polls/poll-detail/'+response.data.data.id})
              }).catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error)
              });
            }else{
              axios.put(apiRoute + '/user/polls/'+this.poll.id, this.poll,  this.$store.getters['auth/token']).then(response => {

                if(this.poll.project){
                  this.$router.push({path: _this.prevRoute});
                }else if(this.poll.profile){
                  this.$router.push({path: '/profile-detail/'+this.poll.profile.id});
                }

              }).catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error)
              });
            }
          }
        });
      },

      getPollType(e) {
        if(!this.poll.fillAnswers.length && this.pollId){
          this.poll.fillAnswers.push({
              "value": ""
            },
            {
              "value": ""
            },
            {
              "value": ""
            }
          );
        }

        this.poll.answerType = e;
      },

      deleteAnswer(key){
        this.answerKey = key;
      },

      addFind: function () {
        if(!this.pollId){
          this.poll.answers.push({ value: '' });
        }else {
          this.poll.fillAnswers.push({ value: '' });
        }

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

      getPoll() {
        axios.get(apiRoute + '/user/polls/'+this.pollId, this.$store.getters['auth/token']).then(response => {
          let _this = this;
          _this.poll = response.data.data;
          response.data.data.answers.forEach((value, key) => {
            _this.poll.fillAnswers.push({
              value: value.answer,
            })
          })

          if(response.data.data.projectId){
            axios.get(apiRoute+ '/user/profiles/null/projects/'+response.data.data.projectId).then(response => {
              _this.poll.projectId = response.data.data;
            }).catch(function (error) {

            });
          }else{
            _this.poll.project = [];
          }


        }).catch(error => {

        })
      },

      deleteMessages(){
        this.errorMessage = '';
      }
    },

    created() {
      this.pollId = this.$route.params.pollId
      this.profileId = this.$route.params.profileId
      bus.$on('deleteAnswer', (data) => {
        if(data == 1){
          this.$delete(this.poll.answers, this.answerKey)
          this.showConfModal = false;
        }
      })
    },

    mounted() {
      bus.$emit('overlay', 1)
      this.getProfiles();
      this.getUser();
      if(this.pollId){
        this.getPoll();
      }else{
        this.poll.answers.push({
            "value": ""
          },
          {
            "value": ""
          },
          {
            "value": ""
          }
        );
      }
    }
  }
</script>

<style lang="scss">
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
  .answer-select {
    .btn {
      background: #e5e5e5;
      transition: 0.3s;
      border-radius: 20px;
      padding: 5px 15px;
      margin-right: 10px;
      font-family: EncodeSansRegular;
      font-size: 1.1rem;
      &:hover {
        background: #cecece;
        transition: 0.3s;
      }
      &.active {
        background: #60d7d6;
        color: #fff;
      }
    }
  }

  .poll-set-responses {
    border-top: 1px solid #bababa;
    color: #474747;
    font-size: 1.1rem;
    .response-list ul {
      list-style: none;
      margin: 0;
      padding: 0;
      li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
        input {
          margin-right: 10px;
        }
        i {
          color: red;
          cursor: pointer;
        }
      }
    }

    .add-response {
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

  .delete-icon{
    right: 2px;
    color: red;
    margin: auto;
    margin-left: 10px;
  }
</style>
