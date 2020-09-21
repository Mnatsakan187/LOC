<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <div class="heading">Add to collection</div>
            <button class="btn close-modal float-left" @click="close">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="enterUrl">Collections</label>
              <v-select
                v-validate="'required'"
                class="modal-select"
                name="collection"
                v-model="collectionId"
                id="enterUrl"
                :options="selectOptions"
                placeholder="Please, select a collection"
              ></v-select>
              <div
                class="invalid-feedback"
                v-if="submitted && errors.has('collection')"
              >Please, select a collection.</div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn cancel" @click="close">
              <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn save" @click="addToCollection()">Add</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import "vue-select/dist/vue-select.css";
export default {
  name: "ModalAddToCollection",
  props: ["collectionableCollections", "type", "collectionableId"],
  data() {
    return {
      collectionId: "",
      submitted: false
    };
  },
  computed: {
    selectOptions() {
      return this.collectionableCollections.map(g => ({
        label: g.name,
        value: g.id
      }));
    }
  },
  methods: {
    addToCollection() {
      this.submitted = true;
      let _this = this;
      this.$validator.validate().then(valid => {
        if (valid) {
          axios
            .post(
              apiRoute +
                "/user/collections/" +
                this.collectionId.value +
                "/" +
                this.type +
                "/" +
                this.collectionableId,
              this.$store.getters["auth/token"]
            )
            .then(response => {
              bus.$emit("close", 1);
              bus.$emit("refresh", { refresh: 1 });
            })
            .catch(error => {});
        }
      });
    },

    close() {
      bus.$emit("close", 1);
    }
  },
  mounted() {}
};
</script>
<style lang="scss">
.modal-select {
  .vs__dropdown-toggle {
    border: none !important;
    background-color: #e5e5e5;
    input {
      color: #323232;
    }
  }
  .vs__selected {
    font-size: 1.2rem;
    color: #323232 !important;
  }
  .vs__clear,
  .vs__open-indicator {
    fill: #323232 !important;
  }
  .vs__dropdown-menu {
    background-color: #e5e5e5 !important;
    .vs__dropdown-option {
      color: #323232;
      &:hover,
      &:focus,
      &.vs__dropdown-option--highlight {
        color: #fff;
        background-color: #323232 !important;
      }
    }
  }
}
</style>
<style scoped lang="scss">
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
  @media (max-width: 600px) {
    vertical-align: bottom;
  }
  .modal-container {
    width: 600px;
    margin: 0px auto;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
    color: #323232;
    @media (max-width: 600px) {
      width: 100%;
    }
    .modal-header {
      border: none;
    }
    .heading {
      font-family: EncodeSansSemiBold;
      font-size: 1.4rem;
    }
    .close-modal {
      font-size: 1.2rem;
    }
    .modal-footer {
      background-color: #474747;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 0;
      justify-content: flex-start;
      .cancel {
        color: #fff;
        font-size: 1.2rem;
        &:hover {
          color: #b9b9b9;
        }
      }
      .save {
        color: #474747;
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
  }
}
/* Modal transition styles */
.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
  @media (max-width: 600px) {
    transform: translateY(200px);
  }
}
</style>
