<template>
  <li>
    <div class="name-dropdown">
      <router-link :to="{ path: '/groups/'+group.id}" class="group-name">{{group.name}}</router-link>
      <div class="action-dropdown" v-click-outside="closeActionDropdown">
        <button
          class="btn dd-toggle"
          :class="{'show': collapsed}"
          v-on:click="toggleActionDropdown"
          type="button"
        >
          <i class="fas fa-ellipsis-h"></i>
        </button>
        <div class="action-dropdown-menu" :class="{'show': collapsed}">
          <router-link class="dd-item" :to="{ path: '/groups/edit/'+group.id}" >Edit</router-link>
          <a class="dd-item"  v-on:click="openModalConfirmation">Delete</a>
        </div>
      </div>
    </div>
    <div class="members" v-if="group.members.length > 0">
      <template v-for="member in group.members.slice(0,3)">
        <img  v-if="member.avatarUri" :src="'/storage/avatarImage/' + member.id + '/' + member.avatarUri"/>
        <img v-else  src="/images/user8-128x128.png"/>
      </template>

      <div class="more-members" v-if="group.members.length >3">+ {{group.members.length - 3}}</div>
    </div>
    <div class="no-members" v-else>No members</div>
  </li>
</template>
<script>
export default {
  name: "GroupItem",
  props: ["group"],
  data: function() {
    return {
      collapsed: false
    };
  },
  methods: {
    toggleActionDropdown() {
      this.collapsed = !this.collapsed;
    },
    closeActionDropdown() {
      this.collapsed = false;
    },
    openModalConfirmation() {
      this.$root.$emit("confirmation modal", true);
      bus.$emit('group', this.group)
    }
  }
};
</script>
