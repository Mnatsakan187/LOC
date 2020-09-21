<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="loc-navigation">
          <router-link class="btn" to="/my-feed">My Feed</router-link>
          <router-link class="btn" to="/discover">Discover</router-link>
        </div>

        <div class="hud-bg">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "hud"
  };
</script>
