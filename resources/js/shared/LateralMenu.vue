<template>
  <div>
    <!-- Mobile Lateral Menu starts -->
    <div class="d-block d-md-none">
      <div
        id="mobileLateralMenu"
        class="mobile-lateral-menu"
        v-bind:class="{ collapsed: showMobileMenu }"
      >
        <a href="javascript:void(0)" class="closebtn" v-on:click="closeMobileMenu">&times;</a>
        <a href="#" class="coming-soon">
          <i class="fas fa-fw fa-envelope"></i> Messages
          <span class="coming-soon">Coming Soon</span>
        </a>

        <a href="#" class="coming-soon mb-5">
          <i class="far fa-fw fa-calendar"></i> Calendar
          <span class="coming-soon">Coming Soon</span>
        </a>

        <router-link v-on:click.native="closeMobileMenu" to="/analytics/followers">
          <i class="fas fa-fw fa-chart-line"></i> Analytics
        </router-link>

        <router-link  v-on:click.native="closeMobileMenu" :to="{ name: 'group.index' }">
          <i class="fas fa-fw fa-users"></i> My Groups
        </router-link>

        <a href="#" class="coming-soon mb-5">
          <i class="fas fa-fw fa-globe-asia"></i> My Network
          <span class="coming-soon">Coming Soon</span>
        </a>

        <router-link v-on:click.native="closeMobileMenu" :to="{ name: 'my.fav.creators'}">
          <i class="fas fa-fw fa-heart"></i> My Favourite Content
        </router-link>

        <a style="cursor: pointer" v-on:click.native="closeMobileMenu"  @click="logout">
          <i class="fas fa-sign-out-alt"></i>
          Log out
        </a>
      </div>
      <!-- Overlay -->
      <div id="overlay" :class="{ show: showMobileOverlay }"></div>
    </div>
    <!-- Mobile Lateral Menu ends -->

    <!-- Desktop Lateral Menu starts -->
    <div class="d-none d-md-block">
      <nav class="desktop-lateral-menu" :class="{ collapsed: showDesktopMenu }">
        <ul class="navbar-primary-menu">
          <li>
            <a href="#" class="btn-expand-collapse" v-on:click="showDesktopMenu = !showDesktopMenu">
              <i class="fas fa-fw fa-bars"></i>
            </a>
          </li>
          <li>
            <router-link :to="{ name: 'my.feed' }">
              <i class="fas fa-fw fa-home"></i>
              <span class="nav-label">HUD</span>
            </router-link>
          </li>
          <li>
            <a href="#" class="disabled" v-tooltip.right="tooltipMsg">
              <i class="loc-icon loc-fw loc-event"></i>
              <span class="nav-label">Events</span>
            </a>
          </li>
          <li>
            <router-link to="/analytics/followers" class="mt-4">
              <i class="fas fa-fw fa-chart-line"></i>
              <span class="nav-label">Analytics</span>
            </router-link>
          </li>
          <li>
            <router-link :to="{ name: 'group.index' }" >
              <i class="fas fa-fw fa-users"></i>Groups
            </router-link>
          </li>
          <li>
            <a href="#" class="disabled" v-tooltip.right="tooltipMsg">
              <i class="fas fa-fw fa-globe-asia"></i>
              <span class="nav-label">My Network</span>
            </a>
          </li>
          <li>
            <router-link :to="{ name: 'my.fav.creators'}" class="mt-4">
              <i class="fas fa-fw fa-heart"></i>
              <span class="nav-label">My favourite content</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
    <!-- Desktop Lateral Menu ends -->
  </div>
</template>

<script>
import Cookies from 'js-cookie'
export default {
  name: "LateralMenu",
  data: function() {
    return {
      tooltipMsg: "Coming Soon",
      showDesktopMenu: true,
      showMobileMenu: false,
      showMobileOverlay: false
    };
  },
  methods: {
    closeMobileMenu: function() {
      this.showMobileMenu = false;
      this.showMobileOverlay = false;
    },

    logout() {
      let _this = this;
      axios.post(apiRoute + '/user/logout',  this.token).then(response => {
        Cookies.remove('token')
        _this.$router.push({path: '/', name: 'welcome'})
      }).catch(error => {

      });
    },
  },
  mounted() {
    this.$root.$on("open mobile menu", openMobileMenu => {
      this.showMobileMenu = openMobileMenu;
      this.showMobileOverlay = true;
    });
  }
};
</script>

<style scoped lang="scss">
/* Overlay starts */
#overlay {
  position: fixed;
  display: none;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1031;
  &.show {
    display: block;
  }
}
/* Overlay ends */

/* Mobile Lateral Menu starts */
.mobile-lateral-menu {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 1032;
  top: 0;
  left: 0;
  background-color: #333;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
  border-top-right-radius: 30px;
  border-bottom-right-radius: 30px;
  &.collapsed {
    width: 80%;
    transition: 0.5s;
  }
  a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    text-overflow: hidden;
    white-space: nowrap;
    font-size: 1rem;
    color: #fcfcfc;
    display: block;
    transition: 0.3s;
    &:hover {
      color: #cacaca;
    }
    i {
      padding-right: 25px;
      font-size: 1.1rem;
      &.loc-fw {
        text-align: center;
        width: 1.25em;
      }
    }
    &.coming-soon {
      color: #505050;
      cursor: pointer;
      span.coming-soon {
        color: #fff;
        border: 1px solid #fff;
        padding: 5px;
        border-radius: 10px;
        font-size: 0.7rem;
        margin-left: 10px;
      }
    }
  }
  .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
  }
}
/* Mobile Lateral Menu ends */

/* Desktop Lateral Menu starts */
.desktop-lateral-menu {
  background-color: #1a1a1a;
  position: fixed;
  bottom: 0px;
  left: 0px;
  top: 0px;
  z-index: 1029;
  width: 200px;
  overflow: hidden;
  -webkit-transition: all 0.1s ease-in-out;
  -moz-transition: all 0.1s ease-in-out;
  transition: all 0.1s ease-in-out;
  padding-top: 70px;
  &.collapsed {
    width: 50px;

    .nav-label {
      display: none;
    }
  }
  .navbar-primary-menu {
    text-align: center;
    margin: 0;
    padding: 0;
    list-style: none;
    li {
      a {
        text-overflow: hidden;
        white-space: nowrap;
        text-decoration: none;
        display: block;
        padding: 10px 18px;
        text-align: left;
        color: #fff;
        font-size: 0.9rem;
        i {
          color: #fff;
          font-size: 1.1rem;
          margin-right: 10px;
          &.loc-fw {
            text-align: center;
            width: 1.25em;
          }
        }
        &:hover {
          background-color: #4a4a4a;
          color: #c2c2c2;
        }
        &.btn-expand-collapse:hover {
          background-color: transparent;
        }
        &.disabled {
          width: 70%;
          color: #505050;
          cursor: default;
          &:hover {
            background-color: transparent;
          }
          i {
            color: #505050;
          }
        }
      }
    }
  }
}
/* Mobile Lateral Menu ends */
</style>
