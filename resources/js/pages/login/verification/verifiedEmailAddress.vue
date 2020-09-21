<template>
  <!-- Main content, verify email -->
  <!--<div class="container mt-3">-->
    <!--<div class="row">-->
      <!--<div class="col-12">-->
        <!--<div class="jumbotron text-center">-->
          <!--<h1>Verify Email Address</h1>-->
          <!--<i-->
            <!--class="fas fa-check-circle"-->
            <!--style="color: #58c824; font-size: 5rem"></i>-->
          <!--<p class="lead mt-2">-->
            <!--Email Address Verified. Please, proceed with login:-->
          <!--</p>-->
          <!--<router-link class="btn btn-primary" :to="{ name: 'login' }">-->
            <!--{{ $t('login') }}-->
          <!--</router-link>-->
        <!--</div>-->
      <!--</div>-->
    <!--</div>-->
  <!--</div>-->

  <div class="container">
    <div class="row mt-5">
      <div class="col-sm-10 col-md-8 offset-sm-1 offset-md-2">
        <div class="jumbotron text-center">
          <h4>Email address was verified!</h4>
          <i class="fas fa-check-circle"></i>
          <p class="lead mt-3">Please, proceed with login:</p>
          <router-link class="btn" :to="{ name: 'login' }">Log In</router-link>
        </div>
      </div>
    </div>
  </div>
  <!-- Optional JavaScript -->



</template>

<script>
  export default {
    name: "verifedEmailAddress",

    data() {
      return {
        token: '',
      }
    },

    created() {
      this.token = this.$route.params.token
    },

    mounted(){
      axios.get(apiRoute + '/user/validate/'+ this.token, {
        token: this.token,
      }).then(response => {

      }).catch(error => {

      })
    }
  }
</script>

<style scoped lang="scss">
  .jumbotron {
    background-color: #000;
    border: 2px solid #fff;
    border-radius: 20px;

    i {
      color: #01ffc3;
      font-size: 3rem;
      margin-top: 20px;
    }

    .btn {
      display: inline-block;
      text-decoration: none;
      color: #fff;
      border: 1px solid #fff;
      border-radius: 20px;
      padding: 10px 15px;
      text-transform: uppercase;
      transition: 0.3s;
      i {
        margin-left: 15px;
      }
      &:hover {
        text-decoration: none;
        color: #01ffc3;
        border: 1px solid #01ffc3;
        transition: 0.3s;
      }
    }
  }
</style>
