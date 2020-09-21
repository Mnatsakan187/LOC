<template>
    <div class="container-fluid create">
      <div class="row create-white">
        <div class="col-sm-12 col-md-8 col-xl-6 offset-md-2 offset-xl-3 mt-5 mb-5 limit-form-width">
          <!--Header-->
          <div class="header">
            <div class="title">
              <div class="heading">{{poll.name}}</div>
              <div class="sub-header">{{dateChange(poll.createdAt)}}</div>
            </div>
            <button @click="closePoll" class="btn close-button">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="content mt-5">
            <!--Form begins-->
            <form>
              <!--Poll question-->
              <div class="question mb-4">{{poll.question}}</div>
              <!--Poll open answer-->
              <div class="open-answer"  v-if="poll.answerType == 1">
                <div class="form-group mt-4">
                  <label class="sr-only" for="pollAnswer">Poll Answer</label>
                  <div v-if="!disableRespondIfNeed">
                     <textarea
                       v-model="pollAnswer"
                       class="form-control"
                       id="pollAnswer"
                       rows="5"
                       placeholder="Write your answer..."
                       v-validate="'required|max:5000'" name="answer"
                     ></textarea>
                    <span class="invalid-feedback" v-if="submitted && errors.has('answer')">{{ errors.first('answer') }}</span>
                  </div>
                  <div v-else >
                     <textarea disabled
                               v-model="poll.userPollAnswer.openAnswer"
                               class="form-control"
                               id="pollAnswer"
                               rows="5"
                     ></textarea>
                  </div>
                </div>
              </div>

              <!-- Poll select response -->
              <div class="response-select" v-if="poll.answerType == 2">
                <div class="form-check" v-for="item in responsesList">
                  <div v-if="!disableRespondIfNeed">
                    <input
                      class="form-check-input"
                      type="radio"
                      id="response"
                      v-bind:value="item.id"
                      v-model="pollAnswer"
                      v-validate="'required'" name="answer"
                    />
                  </div>

                  <div v-else>
                    <input  disabled
                           class="form-check-input"
                           type="radio"
                           name="response"
                           id="response"
                           v-bind:value="item.id"
                           v-model="poll.userPollAnswer.selectAnswerId"
                    />
                  </div>

                  <label class="form-check-label" for="exampleRadios1">{{item.answer}}</label>
                </div>
                <span class="invalid-feedback" v-if="submitted && errors.has('answer')">{{ errors.first('answer') }}</span>
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
            <router-link :to="{ path: prevRoute}"  class="btn cancel">
              <i class="fas fa-times"></i> Cancel
            </router-link>
            <button type="button"
                    class="btn save"
                    @click="saveAnswer(poll.id, poll.answerType)"
                    :disabled="disableRespondIfNeed"
                    :style="[disableRespondIfNeed ? {'cursor': 'not-allowed'} : '']">Respond</button>
          </div>
        </div>
      </div>
      <div class="loading" v-show="showLoader">
        <span class="fa fa-spinner fa-spin"></span>
      </div>
    </div>
</template>

<script>
  export default {
    name: "pollView",
    middleware: 'auth',
    data() {
      return {
        responsesList: [],
        prevRoute: '',
        poll: {},
        pollAnswer: '',
        showLoader: false,
        submitted: false,
        notificationId: ''
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

    computed: {
      disableRespondIfNeed() {
        return this.poll.disableRespond
      }

    },

    methods:{
      getPoll() {
        axios.get(apiRoute + '/user/polls/'+this.pollId, this.$store.getters['auth/token']).then(response => {
          let _this = this;
          _this.poll = response.data.data;
          _this.responsesList = response.data.data.answers;
          console.log(this.poll)
        }).catch(error => {

        })
      },

      dateChange: function (date) {
        return moment(date).format("MMM DD");
      },

      closePoll(){
        if(this.poll.profileId){
          this.$router.push({ path: '/profile-detail/'+this.poll.profileId })
        }else{
          this.$router.push({ path: '/project-detail/project/'+this.poll.projectId})
        }

      },


      saveAnswer(pollId, type) {
        let _this = this;
        let data = {
          selectAnswerId: type == 2 ?  this.pollAnswer : '',
          openAnswer: type == 1 ?  this.pollAnswer : '',
          answerType: type
        }

        this.submitted = true;
        this.$validator.validate().then(valid => {
          if(valid){
            this.showLoader = true;
            if (data.openAnswer || data.selectAnswerId) {
              axios.post(apiRoute + '/user/polls/' + pollId + '/answers', data,  this.$store.getters['auth/token']).then(response => {
                this.showLoader = false;
                if(this.poll.profileId){
                  this.$router.push({path: '/profile-detail/'+this.poll.profileId})
                }else {
                  axios.get(apiRoute+ '/user/projects/'+this.poll.projectId).then(response => {
                    this.$router.push({path: '/project-detail/project/'+response.data.data.id})
                  }).catch(function (error) {

                  });
                }
              }).catch(error => {
                this.showLoader = false;
              })
            }
          }

        });
      },

      updateNotification(){
        axios.get(apiRoute + '/notifications/read/'+this.notificationId, this.$store.getters['auth/token']).then(response => {

        }).catch(error => {

        })
      }
    },

    created() {
      this.pollId = this.$route.params.pollId
      this.notificationId = this.$route.params.notificationId
    },


    mounted() {
      $(".dropdown-menu-notification").removeClass('show');
      if(this.notificationId){
        this.updateNotification();
        bus.$emit('notRefresh', 1)
      }
      this.getPoll();
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
  .limit-form-width {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
  }
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #474747;
    .heading {
      font-family: EncodeSansSemiBold;
      font-size: 1.6rem;
    }
    .sub-header {
      font-size: 0.8rem;
      text-transform: uppercase;
    }
    .close-button {
      color: #474747;
      font-size: 1.6rem;
    }
  }

  .question {
    color: #474747;
    font-size: 1.2rem;
  }

  .response-select {
    color: #474747;
    font-size: 1rem;
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
