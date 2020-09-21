<template>
  <div class="member">
    <div class="member-area">
      <img  v-if="member.avatarUri" :src="'/storage/avatarImage/' + member.id + '/' + member.avatarUri"/>
      <img v-else  src="/images/user8-128x128.png"/>
      <div class="member-info">
        <div class="member-name" v-if="member.user && member.user">{{member.user.firstName}}  {{member.user.lastName}}</div>
        <div class="member-name" v-if="member.firstName && member.lastName">{{member.firstName}} {{member.lastName}}</div>
        <div class="creative-title" v-if="member.creativeTitle">{{member.creativeTitle}}</div>
      </div>
      <button
        class="btn btn-delete float-right"
        data-toggle="collapse"
        :data-target="'#collapseDelete'+ member.id"
        aria-expanded="false"
        aria-controls="collapseDelete"
      >Delete</button>
    </div>

    <div class="collapse" :id="'collapseDelete'+member.id">
      <div class="delete-question">
        Do you want to delete this member?
        <div class="answer-buttons">

          <button class="btn btn-yes" v-if="group" @click="deleteMember(group.id, member.id)">
            <i class="fas fa-check"></i> Yes
          </button>

          <button class="btn btn-yes" v-else @click="deleteTeam(member.id)">
            <i class="fas fa-check"></i> Yes
          </button>

          <button
            class="btn btn-no"
            data-toggle="collapse"
            :data-target="'#collapseDelete'+ member.id"
            aria-expanded="false"
            aria-controls="collapseDelete"
          >
            <i class="fas fa-times"></i> No
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "MemberItem",
  data: function() {
    return {};
  },
  props: ["member", "group", 'deleteId', 'type'],
  methods: {
    deleteMember(groupId, memberId) {
      axios.delete(apiRoute+ '/user/groups/'+groupId+'/members/'+memberId).then(response => {
        bus.$emit('refresh', 1)
      }).catch(function (error) {

      });
    },

    deleteTeam(userId) {
      let _this = this;
      axios.delete(apiRoute+ '/user/'+this.type+'/'+this.deleteId+'/teams/'+userId).then(response => {
        bus.$emit('refresh', 1)
      }).catch(function (error) {

      });
    },
  }
};
</script>
<style scoped lang="scss">
.member {
  border-bottom: 1px solid #e5e5e5;
}
.member-area {
  width: 100%;
  display: flex;
  justify-content: left;
  padding: 10px;
  img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
  }
  .member-info {
    width: 100%;
    .member-name {
      font-family: EncodeSansSemiBold;
      font-size: 1.2rem;
    }
    .creative-title {
      font-size: 1.1rem;
    }
  }
  .btn-delete {
    &:hover {
      color: #9d72ff;
      transition: 0.5s;
    }
  }
}

.delete-question {
  text-align: right;
  padding: 10px;
  @media (max-width: 550px) {
    text-align: center;
  }
  .answer-buttons {
    display: inline-block;
    @media (max-width: 550px) {
      display: block;
    }
    .btn {
      color: #474747;
      border: 1px solid #474747;
      border-radius: 20px;
      padding: 5px 20px;
      font-family: EncodeSansSemiBold;
      margin-left: 10px;
      @media (max-width: 550px) {
        padding: 5px;
      }
      &:hover {
        color: #9d72ff;
        border: 1px solid #9d72ff;
      }
      &.btn-no {
        background: #474747;
        color: #fff;
        &:hover {
          background: #9d72ff;
        }
      }
    }
  }
}
</style>
