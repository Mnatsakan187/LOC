<template>
  <div class="layoutCreate">
    <navbar />
    <div >
      <child />

    </div>
    <div>
      <lateral-menu></lateral-menu>
      <vfooter />
    </div>
  </div>
</template>

<script>
  import Navbar from '~/shared/Navbar'
  import Vfooter from '~/shared/Vfooter'
  import LocaleDropdown from "../shared/LocaleDropdown";
  import LateralMenu from "../shared/LateralMenu";

  export default {
    name: 'layoutCreate',
    components: {
      LocaleDropdown,
      Navbar,
      Vfooter,
      LateralMenu
    }
  }
</script>
<style lang="scss" scoped>
  .layoutCreate {
    /*padding to avoid content ovelapped with fixed navbar */
    height: 100%;
    background-color: #474747;
    padding-top: 60px;

  }
</style>
<style>
  #app{
    overflow-y: auto;
  }


  @media (max-width: 767px) {
    .layoutCreate{
      padding-top: 0px !important;
    }


  }
</style>
