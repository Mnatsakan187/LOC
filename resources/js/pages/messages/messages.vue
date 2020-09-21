<template>
  <div  class="container-fluid h-100 col-md-12 col-xl-12 ">
    <div class="row justify-content-center h-100">
      <div class="col-md-3 col-xl-2 chat">
        <div class="card mb-sm-3 mb-md-0 contacts_card friends-block">
          <div class="card-body contacts_body">
            <ul class="contacts">
              <template v-for="user, index in sortedUsersArray">
                <li   @click="selectUser(index, user.id)"  v-bind:class="{ active : key == index && selected == true }">
                  <div class="d-flex bd-highlight">
                    <div class="img_cont">
                      <img  class="rounded-circle user_img" v-if="user.avatarUri"
                            :src="'/storage/avatarImage/' + user.id + '/' + user.avatarUri"/>
                      <img class="rounded-circle user_img" v-else src="/images/user8-128x128.png"/>
                    </div>
                    <div class="user_info">
                      <span>{{user.firstName}} {{user.lastName}}</span>
                      <p v-if="user.messagesReceiveCount != 0 || user.messagesSentCount != 0">{{user.messagesReceiveCount + user.messagesSentCount}} messages</p>
                    </div>
                  </div>
                </li>
              </template>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-8 col-xl-6 chat">
        <div class="card">
          <div class="card-body msg_card_body">
            <template v-for="message in messages">
              <div v-if="message.fromUserId == user.id" class="d-flex justify-content-end mb-4">
                <div class="msg_cotainer_send">
                  {{message.message}}
                  <span class="msg_time_send">{{dateChange(message.createdAt)}}</span>
                </div>
                <div class="img_cont_msg">

                  <img  class="rounded-circle user_img" v-if="message.fromUser.avatarUri"
                        :src="'/storage/avatarImage/' + message.fromUser.id + '/' + message.fromUser.avatarUri"/>
                  <img class="rounded-circle user_img" v-else src="/images/user8-128x128.png"/>
                </div>
              </div>

              <div v-else class="d-flex justify-content-start mb-4">
                <div class="img_cont_msg">
                  <img  class="rounded-circle user_img" v-if="message.fromUser.avatarUri"
                        :src="'/storage/avatarImage/' + message.fromUser.id + '/' + message.fromUser.avatarUri"/>
                  <img class="rounded-circle user_img" v-else src="/images/user8-128x128.png"/>
                </div>
                <div class="msg_cotainer">
                  {{message.message}}
                  <span class="msg_time">{{dateChange(message.createdAt)}}</span>
                </div>
              </div>
            </template>
            <div class="choose-friend" v-if="messages.length == 0  && !toUserId">
              <p>Choose Friend</p>
            </div>
            <div class="choose-friend" v-if="messages.length == 0 && toUserId">
              <p>You don't have any messages</p>
            </div>
          </div>
          <div class="card-footer">
            <div style="position:relative;">
              <textarea v-validate="'required|max:191'" v-model="message" name="message" class="form-control type_msg"
                        @keyup.enter="trigger" @keydown="inputHandler" placeholder="Write new message"> </textarea>
              <div class="button-style">
                <button @click="sendMessage" style="background: #159bd5" class="btn btn-primary" ref="sendReply">SEND</button>
              </div>
            </div>
          </div>
          <span class="error-message" v-if="submitted && errors.has('message')">{{ errors.first('message') }}</span>
          <span class="error-message" v-if="error.get('toUserId') &&  !errors.has('message') && !toUserId">Choose the friend to send messages</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
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
    name: 'messages',
    data: () => ({
      users: [],
      key: '',
      toUserId: '',
      message: '',
      messages: [],
      selected: false,
      user:{},
      submitted:false,
      error: new error(),
    }),

    methods: {
      getUsers() {
        let _this = this;
        axios.get(apiRoute + '/users?exception=2',  this.$store.getters['auth/token']).then(response => {
          _this.users = response.data.data;
        }).catch(error => {

        });
      },

      sendMessage() {
        this.submitted = true;
        this.$validator.validate().then(valid => {
          if (valid) {
            let _this = this;
            axios.post(apiRoute + '/user/messages',
              {toUserId:this.toUserId, isRead:0, message:this.message},  this.$store.getters['auth/token']).then(response => {
              this.messages.push(response.data.data)
              _this.message = ''
              _this.submitted = false;
            }).catch(error => {
              _this.error.record(error.response.data.error)
            });
          }
        });
      },

      inputHandler(e) {
        if (e.keyCode === 13 && !e.shiftKey) {
          e.preventDefault();
        }
      },

      trigger () {
        this.$refs.sendReply.click()
      },

      getUser() {
        let _this = this;
        axios.get(apiRoute + '/user',  this.$store.getters['auth/token']).then(response => {
          _this.user = response.data.data;
        }).catch(error => {

        });
      },

      dateChange: function (date) {
        return moment(date).format("dddd  h:mm:ss a");
      },

      getMessages(toUserId) {
        let _this = this;
        axios.get(apiRoute + '/user/messages/'+toUserId, this.$store.getters['auth/token']).then(response => {
          _this.messages = response.data.data
        }).catch(error => {

        });
      },

      getMessage(messageId, userId) {
        if(userId == this.user.id) {
          let _this = this;
          axios.get(apiRoute + '/user/messages/'+messageId+'/show', this.$store.getters['auth/token']).then(response => {
            _this.messages.push(response.data.data);
          }).catch(error => {

          });
        }

      },

      selectUser(key, userId){
        this.key = key;
        this.toUserId = userId;
        this.selected = true;
        if(userId){
          this.getMessages(userId);
        }
      }
    },

    computed: {
      sortedUsersArray: function() {
        function compare(a, b) {
          if (b.messagesCreatedAt < a.messagesCreatedAt)
            return -1;
          if (b.messagesCreatedAt > a.messagesCreatedAt)
            return 1;
          return 0;
        }
        return this.users.sort(compare);
      },
    },
    mounted () {
      this.getUsers()
      this.getUser();

      window.Echo.channel('message').listen('NewMessage', (e) =>{
        this.getMessage(e.messageId, e.userId);
        this.getUsers();
      })

    }

  }
</script>

<style scoped>
  body, html {
    height: 100%;
    margin: 0;
    background: #7F7FD5;
    background: -webkit-linear-gradient(to right, #91EAE4, #86A8E7, #7F7FD5);
    background: linear-gradient(to right, #91EAE4, #86A8E7, #7F7FD5);
  }


  .chat {
    margin-top: auto;
    margin-bottom: auto;
  }

  .card {
    height: 800px;
  }

  .contacts_body {
    padding: 0.75rem 0 !important;
    overflow-y: auto;
    white-space: nowrap;
  }

  .msg_card_body {
    overflow-y: auto;
  }

  .type_msg {
    border: 0 !important;
    overflow-y: auto;
  }

  .type_msg:focus {
    box-shadow: none !important;
    outline: 0px !important;
  }


  .contacts {
    list-style: none;
    padding: 0;
  }

  .contacts li {
    width: 100% !important;
    padding: 5px 10px;
    margin-bottom: 15px !important;
  }

  .active {
    background-color: #e7f5fa;
  }

  .user_img {
    height: 50px;
    width: 50px;

  }

  .img_cont {
    position: relative;
    height: 55px;
    margin-top: 9px;

  }

  .img_cont_msg {
    height: 40px;
    width: 40px;
  }


  .user_info {
    margin-bottom: auto;
    margin-top: 9px;
  }

  .user_info span {
    font-size: 20px;
  }

  .user_info p {
    font-size: 12px;
  }


  .video_cam span {
    color: white;
    font-size: 20px;
    cursor: pointer;
    margin-right: 20px;
  }

  .msg_cotainer {
    margin-top: auto;
    margin-bottom: auto;
    margin-left: 10px;
    border-radius: 25px;
    background-color: #82ccdd;
    padding: 10px;
    position: relative;
  }

  .msg_cotainer_send {
    margin-top: auto;
    margin-bottom: auto;
    margin-right: 10px;
    border-radius: 25px;
    background-color: #f2f2f2;
    padding: 10px;
    position: relative;
  }

  .msg_time {
    position: absolute;
    left: 0;
    bottom: -15px;
    color: black;
    font-size: 10px;
  }

  .msg_time_send {
    position: absolute;
    right: 0;
    bottom: -15px;
    color: black;
    font-size: 10px;
  }


  .action_menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .action_menu ul li {
    width: 100%;
    padding: 10px 15px;
    margin-bottom: 5px;
  }

  .action_menu ul li i {
    padding-right: 10px;

  }

  .action_menu ul li:hover {
    cursor: pointer;
    background-color: rgba(0, 0, 0, 0.2);
  }

  @media (max-width: 576px) {
    .contacts_card {
      margin-bottom: 15px !important;
    }
  }

  .container-fluid {
    margin-top: 80px;
  }

  .card {
    border:none;
  }

  .friends-block{
    border-right: 1px solid black;
    border-radius: 0px;
  }



  .user_info span{
    color: black;
  }

  .card-footer{
    padding: 0;
    background-color: #0000;
    border: 1px solid #bcbcbc;
    border-radius: 4px;
  }

  .type_msg{
    overflow: hidden;
  }

  .button-style{
    position: absolute;
    right: 13px;
    top: 25%
  }

  .choose-friend{
    text-align: center;
    margin-top: 340px;
    color: #bcbcbc;
    font-size: 20px;

  }

  .error-message {
    color: red;
    margin-top: 10px;
  }

</style>
