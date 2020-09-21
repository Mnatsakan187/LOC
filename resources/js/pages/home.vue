<template>
  <hud-welcome> </hud-welcome>
</template>

<script>
import HudWelcome from "./hud/hud-welcome";
import Cookies from "js-cookie";
export default {
  components: { HudWelcome },
  middleware: "auth",

  metaInfo() {
    return { title: this.$t("home") };
  },
  mounted() {}
};
</script>
