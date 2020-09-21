0<template>
  <div class="loader">
    <div class="lds-dual-ring"></div>
  </div>
</template>

<script>
export default {
  name: "Loader"
};
</script>

<style scoped lang="scss">
.loader {
  width: 100%;
  text-align: center;
  .lds-dual-ring {
    display: inline-block;
    width: 40px;
    height: 40px;
  }
  .lds-dual-ring:after {
    content: " ";
    display: block;
    width: 36px;
    height: 36px;
    margin: 1px;
    border-radius: 50%;
    border: 5px solid #fff;
    border-color: #fff transparent #fff transparent;
    animation: lds-dual-ring 1.2s linear infinite;
  }
  @keyframes lds-dual-ring {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
}
</style>
