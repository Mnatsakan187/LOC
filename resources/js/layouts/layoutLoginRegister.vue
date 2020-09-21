<template>
  <div class="layout-login-register">
    <child />
  </div>
</template>

<script>
import Navbar from "~/shared/Navbar";
import Vfooter from "~/shared/Vfooter";
import LocaleDropdown from "../shared/LocaleDropdown";
import LateralMenu from "../shared/LateralMenu";

export default {
  name: "MainLayout",
  components: {
    LocaleDropdown,
    Navbar,
    Vfooter,
    LateralMenu
  }
};
</script>

<style>
#app {
  overflow-y: auto;
}

.layout-login-register {
  padding-top: 60px;
  padding-bottom: 80px;
}

@media (max-width: 767px) {
  .layout-login-register {
    padding-top: 0px;
  }

  .login {
    padding-top: 60px;
  }
}
</style>

