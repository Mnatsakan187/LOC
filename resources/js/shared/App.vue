<template>
  <div id="app">
    <loading ref="loading" />
    <transition name="page" mode="out-in">
      <component :is="layout" v-if="layout" />
    </transition>
  </div>
</template>

<script>
import Loading from "./Loading";
// Load layout components dynamically.
const requireContext = require.context("~/layouts", false, /.*\.vue$/);

const layouts = requireContext
  .keys()
  .map(file => [file.replace(/(^.\/)|(\.vue$)/g, ""), requireContext(file)])
  .reduce((components, [name, component]) => {
    components[name] = component.default || component;
    return components;
  }, {});

export default {
  el: "#app",

  components: {
    Loading
  },

  data: () => ({
    layout: null,
    defaultLayout: "default",
    layoutLoginRegister: "layoutLoginRegister",
    layoutsCreate: "layoutsCreate"
  }),

  metaInfo() {
    const { appName } = window.config;

    return {
      title: appName,
      titleTemplate: `%s · ${appName}`
    };
  },

  mounted() {
    this.$loading = this.$refs.loading;
  },

  methods: {
    /**
     * Set the application layout.
     *
     * @param {String} layout
     */
    setLayout(layout) {
      if (!layout || !layouts[layout]) {
        layout = this.defaultLayout;
      }

      this.layout = layouts[layout];
    },

    setLayoutBasic(layout) {
      if (!layout || !layouts[layout]) {
        layout = this.layoutLoginRegister;
      }

      this.layout = layouts[layout];
    },

    createLayout(layout) {
      if (!layout || !layouts[layout]) {
        layout = this.layoutsCreate;
      }

      this.layout = layouts[layout];
    }
  }
};
</script>
<style lang="scss">
@import "../../assets/css/custom-fonts.css";

html {
  overflow-x: hidden;
}

body {
  /*padding to avoid content ovelapped with fixed navbar */
  /* padding-top: 60px;*/
  /* preventing horizontal scroll */
  overflow-x: hidden;
  background-color: #000;
  /*@media (max-width: 767px) {
    padding-top: 50px;
    padding-bottom: 64px;
  }*/
}
.container-fluid {
  /*padding-left: 60px;
  @media (max-width: 767px) {
    padding: 0;
  }*/
}

/* Vue tooltip styles start */
.vue-tooltip {
  display: block !important;
  z-index: 10000;

  .tooltip-inner {
    background: #62666a;
    color: white;
    border-radius: 16px;
    padding: 5px 10px 4px;
  }

  .tooltip-arrow {
    width: 0;
    height: 0;
    border-style: solid;
    position: absolute;
    margin: 5px;
    border-color: #62666a;
    z-index: 1;
  }

  &[x-placement^="top"] {
    margin-bottom: 5px;

    .tooltip-arrow {
      border-width: 5px 5px 0 5px;
      border-left-color: transparent !important;
      border-right-color: transparent !important;
      border-bottom-color: transparent !important;
      bottom: -5px;
      left: calc(50% - 5px);
      margin-top: 0;
      margin-bottom: 0;
    }
  }

  &[x-placement^="bottom"] {
    margin-top: 5px;

    .tooltip-arrow {
      border-width: 0 5px 5px 5px;
      border-left-color: transparent !important;
      border-right-color: transparent !important;
      border-top-color: transparent !important;
      top: -5px;
      left: calc(50% - 5px);
      margin-top: 0;
      margin-bottom: 0;
    }
  }

  &[x-placement^="right"] {
    margin-left: 5px;
    .tooltip-arrow {
      border-width: 5px 5px 5px 0;
      border-left-color: transparent !important;
      border-top-color: transparent !important;
      border-bottom-color: transparent !important;
      left: -5px;
      top: calc(50% - 5px);
      margin-left: 0;
      margin-right: 0;
    }
  }

  &[x-placement^="left"] {
    margin-right: 5px;
    .tooltip-arrow {
      border-width: 5px 0 5px 5px;
      border-top-color: transparent !important;
      border-right-color: transparent !important;
      border-bottom-color: transparent !important;
      right: -5px;
      top: calc(50% - 5px);
      margin-left: 0;
      margin-right: 0;
    }
  }

  &.popover {
    $color: #f9f9f9;

    .popover-inner {
      background: $color;
      color: #62666a;
      padding: 24px;
      border-radius: 5px;
      box-shadow: 0 5px 30px rgba(#62666a, 0.1);
    }

    .popover-arrow {
      border-color: $color;
    }
  }

  &[aria-hidden="true"] {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.15s, visibility 0.15s;
  }

  &[aria-hidden="false"] {
    visibility: visible;
    opacity: 1;
    transition: opacity 0.15s;
  }
}
/* Vue tooltip styles end */

/* Empty content styles start */
.empty-content-discover {
  padding-top: 30px;
  text-align: center;
  .line {
    padding-bottom: 20px;
    color: #fff;
    font-family: EncodeSansRegular;
    font-size: 1.2rem;
    &.bolder {
      font-family: EncodeSansBold;
      font-size: 1.4rem;
    }
  }
  .click-discover {
    color: #fff;
    border: 2px solid #fff;
    border-radius: 10px;
    font-size: 1.4rem;
    transition: 0.5s;
    &:hover {
      color: #9d72ff;
      border: 2px solid #9d72ff;
      transition: 0.5s;
    }
  }
}
/* Empty content styles end */

/* LOC navigation starts */
.loc-navigation {
  margin-top: 20px;
  text-align: left;
  padding-bottom: 20px;
  .btn {
    text-transform: uppercase;
    color: #aeaeae;
    display: inline-block;
    padding-bottom: 5px;
    position: relative;
    transition: color 0.1s;
    font-size: 0.9rem;
    &:hover {
      color: #e4e4e4;
      transition: color 0.1s;
    }
    &:before {
      content: "";
      position: absolute;
      width: 15%;
      height: 1px;
      bottom: 0;
      left: 40%;
      border-bottom: 3px solid transparent;
      transition: border-bottom 0.1s;
    }
    &.router-link-exact-active {
      color: #e4e4e4;
      &:before {
        border-bottom: 3px solid #e4e4e4;
        transition: border-bottom 0.1s;
      }
    }
  }
  @media (max-width: 767px) {
    padding-top: 10px;
    text-align: center;
  }
}

.modal-delete .modal-footer {
  background: #fafbfc;
  text-align: center;
  border-top: none;
  padding: 30px;
  border-radius: 0 0 10px 10px;
}

.modal-delete .modal-footer .btn-light-grey {
  color: #a1acb5;
}

.modal-delete .btn-red {
  background-color: #f69798;
}
.modal-delete .btn-large {
  padding: 13px 30px;
  color: white;
  font-weight: 600;
  font-size: 14px;
  border-radius: 5px;
}

.modal-delete .btn-large {
  padding: 13px 30px;
  color: white;
  font-weight: 600;
  font-size: 14px;
  border-radius: 5px;
}

.modal-delete .btn-light-grey {
  background-color: #e9edef;
}

.modal-delete .modal-footer {
  background: #fafbfc;
  text-align: center;
  border-top: none;
  padding: 30px;
  border-radius: 0 0 10px 10px;
}

.modal-delete .modal-content {
  border-radius: 10px;
}

.modal-delete .modal-dialog .header-section,
.modal-dialog-wide .header-section {
  display: block;
}

.modal-delete .modal-content {
  border-radius: 5px;
  box-shadow: none;
  border: 0;
}

.modal-delete .header-section .header-section-white {
  background: white;
}

.modal-delete .header-section .header-section-white {
  background: white;
}

.modal-delete .header-section {
  background: #fafbfc;
  padding: 30px;
  text-align: center;
  border-radius: 10px 10px 0 0;
}

.modal-delete .modal-dialog .header-section h2,
.modal-dialog-wide .header-section h2 {
  font-weight: 600;
  color: #4c5962;
}

.modal-delete .header-section {
  background: #fafbfc;
  padding: 30px;
  text-align: center;
  border-radius: 10px 10px 0 0;
}

.modal-delete .color-light-blue,
.color-light-blue::before {
  color: #c5daea;
}

.modal-delete .color-light-blue,
.color-light-blue::before {
  color: #c5daea;
}

.modal-delete .fa.pull-right,
.timeline .timeline-post .comment-info .pull-right.action-link.action-remove,
.pull-right.action-link.action-remove,
.pull-right.popover-icon {
  margin-left: 0.3em;
}

.modal-delete .modal-dialog .header-section h1,
.modal-dialog .header-section h2,
.modal-dialog-wide .header-section h1,
.modal-dialog-wide .header-section h2 {
  font-size: 16px;
  font-weight: 400;
  margin: 0;
  color: #77828b;
  width: 100%;
}

.modal-delete .text-center {
  margin: auto;
}

.d-flex > *,
.d-inline-flex > * {
  flex: 0 1 auto !important;
}

.invalid-feedback {
  display: block !important;
  color: #ff90fc;
  margin-top: 10px;
}

/* Common style overrides start */
.form-control:focus {
  box-shadow: 0 0 0 0.2rem rgba(157, 114, 255, 0.5);
}
.btn.focus,
.btn:focus {
  box-shadow: 0 0 0 0.2rem rgba(157, 114, 255, 0.5);
}
/* Common style overrides end */

.register-select {
  .vs__dropdown-toggle {
    border: none !important;
    border-bottom: 1px solid #fff !important;
    border-radius: 0px !important;
    padding: 10px 0 !important;
    input {
      color: #fff;
    }
  }
  .vs__selected {
    font-size: 1.2rem;
    color: #fff !important;
  }
  .vs__selected-options {
    flex-wrap: inherit !important;
  }
  .vs__clear,
  .vs__open-indicator {
    fill: #fff !important;
  }
  .vs__dropdown-menu {
    background-color: #2f2f2f !important;
    .vs__dropdown-option {
      color: #fff !important;
      &:hover,
      &:focus,
      &.vs__dropdown-option--highlight {
        background-color: #000 !important;
      }
    }
  }
}

.buttons-area {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #292929;
  padding: 20px;
  text-align: center;
  .cancel-link {
    color: #fff;
    text-decoration: none;
    font-size: 1.2rem;
    padding: 10px;
    transition: 0.3s;
    &:hover {
      color: #949494;
      transition: 0.3s;
    }
  }
  .continue-button {
    color: #292929;
    font-size: 1.2rem;
    text-decoration: none;
    background-color: #fff;
    padding: 10px 60px;
    border-radius: 30px;
    margin-left: 20px;
    &:hover {
      opacity: 0.7;
      transition: 0.3s;
    }
  }
}

.loading {
  text-align: center;
  position: fixed;
  color: #fff;
  z-index: 9;
  padding: 8px 18px;
  border-radius: 5px;
  left: calc(50% - 45px);
  top: calc(50% - 18px);
  font-size: 40px;
}

.pointer {
  cursor: pointer;
}

.cursor-pointer-color {
  color: #292929 !important;
  cursor: pointer;
}

@media (max-width: 500px) {
  .continue-button {
    padding: 9px 30px !important;
  }
}

.ck-editor__editable {
  min-height: 300px;
  font-size: 1.2rem;
  color: #474747 !important;
  border: none;
  background-color: #e5e5e5;
}
</style>
