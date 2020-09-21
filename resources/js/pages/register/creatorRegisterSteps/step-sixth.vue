<template>
  <!-- Main content, verify email -->
  <div class="container">
    <div class="row mt-5">
      <div class="col-sm-10 col-md-8 offset-sm-1 offset-md-2">
        <div class="jumbotron">
          <h4>Hello, {{creatorParam.firstName}} {{creatorParam.lastName}}!</h4>
          <p class="lead">
            <i class="fas fa-exclamation-circle"></i>
            A verification link has been sent to your email account.
          </p>

          <p>Please, click on the link that has just been sent to your email account to verify your email and continue the registration process.</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Optional JavaScript -->
</template>

<script>
  export default {
    name: "step-sixth",
    middleware: "guest",
    props: ['creatorParam'],
    data() {
      return {

      }
    },

    methods: {

    }
  }
</script>

<style scoped lang="scss">
  .jumbotron {
    background-color: #000;
    border: 2px solid #fff;
    border-radius: 20px;

    i {
      color: #9d72ff;
    }
  }
</style>
