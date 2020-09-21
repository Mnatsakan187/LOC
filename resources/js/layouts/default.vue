<template>
  <div class="main-layout">
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
import HeadNavbar from "../shared/head-navbar";
import LocaleDropdown from "../shared/LocaleDropdown";
import LateralMenu from "../shared/LateralMenu";

export default {
  name: 'MainLayout',

  components: {
    LocaleDropdown,
    HeadNavbar,
    Navbar,
    Vfooter,
    LateralMenu
  }
}
</script>

<style>
  #app{
    /*overflow-y: unset !important;*/
    height: 100%;
  }

  .main-layout{
    margin-top: 60px;
    padding-left: 50px;
  }


  @media (max-width: 767px) {
    .main-layout{
      padding-top: 51px;
      padding-bottom: 64px;
      padding-left: 0px;
      margin-top: 0px;
    }
  }

</style>

