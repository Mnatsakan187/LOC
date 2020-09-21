<template>
  <div class="tile tile-collection">
    <router-link :to="{name: 'collection.detail', params: {id:item.id, item: item.allValue }}">
      <div class="info-dropdown d-flex justify-content-center align-items-center">
        <div class="info">
          <span class="title">{{item.name}}</span>
          <span v-if="item.collectionBadge" class="updated-badge">NEW</span>
        </div>
      </div>
      <img
        v-if="item.avatarUri"
        :class="{'updated': item.color}"
        :src="'/storage/collections/' + item.id + '/' + item.avatarUri"
        class="tile-image"
      />
      <img v-else :class="{'updated': item.color}" src="/images/collection.png" class="tile-image" />
      <div class="overlay g"></div>
    </router-link>
  </div>
</template>

<script>
export default {
  name: "TileCollection",
  props: ["item"]
};
</script>
