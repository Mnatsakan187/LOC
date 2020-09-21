<template>
  <div class="container-fluid create">
    <div class="row create-white">
      <div
        class="col-sm-12 col-md-8 col-lg-6 col-xl-4 offset-md-2 offset-lg-3 offset-xl-4 mt-5 mb-5"
      >
        <div class="header">
          <div class="heading" v-if="!groupId">New Group</div>
          <div class="heading" v-else>Edit Group</div>
          <router-link class="btn close-button" :to="{ path: prevRoute}">
            <i class="fas fa-times"></i>
          </router-link>
        </div>
        <div class="content mt-5">
          <form>
            <div class="form-group">
              <label class="sr-only" for="groupName">GroupName</label>
              <input
                v-validate="'required'"
                name="name"
                v-model="group.name"
                type="text"
                class="form-control"
                id="groupName"
                placeholder="Group Name"
              />
              <span
                class="invalid-feedback"
                v-if="error.get('name') && !errors.has('name')"
              >{{error.get('name')}}</span>
              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('name')"
              >{{ errors.first('name') }}</span>
            </div>
            <!--Group description-->
            <div class="form-group mt-4">
              <label class="sr-only" for="groupDescription">Group Description</label>
              <ckeditor
                id="groupDescription"
                name="description"
                v-validate="'required|max:1000'"
                v-model="group.description"
                :editor="editor"
                :config="editorConfig"
              ></ckeditor>
              <span
                class="invalid-feedback"
                v-if="submitted && errors.has('description')"
              >{{ errors.first('description') }}</span>
              <span class="invalid-feedback" v-text="error.get('description')"></span>
            </div>
            <div class="form-group">
              <label class="sr-only" for="groupMembers">Add Members</label>
              <v-select
                id="groupMembers"
                multiple
                class="members-select"
                v-model="group.user"
                :options="users"
                label="id"
                placeholder="Add Members"
              >
                <template v-slot:selected-option="member">
                  <div class="option-img-name">
                    <img
                      v-if="member.avatarUri"
                      :src="'/storage/avatarImage/' + member.id + '/' + member.avatarUri"
                    />
                    <img v-else src="/images/user8-128x128.png" />
                    <span class="name">{{member.name}}</span>
                  </div>
                </template>
                <template v-slot:option="member">
                  <div class="option-img-name">
                    <img
                      v-if="member.avatarUri"
                      :src="'/storage/avatarImage/' + member.id + '/' + member.avatarUri"
                    />
                    <img v-else src="/images/user8-128x128.png" />
                    <div class="name-title">
                      <span class="name">{{member.name}}</span>
                      <!--<span class="title">{{member.creativeTitle}}</span>-->
                    </div>
                  </div>
                </template>
              </v-select>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="row create-gray">
      <div class="col-sm-12 col-md-6 offset-md-3">
        <div class="action-buttons">
          <router-link class="btn cancel" :to="{ path: prevRoute}">
            <i class="fas fa-times"></i> Cancel
          </router-link>
          <button v-if="!groupId" type="button" class="btn save" @click="validationAddGroup">Save</button>
          <button v-else type="button" class="btn save" @click="validationAddGroup">Edit</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import "vue-select/dist/vue-select.css";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
class error {
  constructor() {
    this.error = {};
  }

  get(field) {
    if (this.error[field]) {
      return this.error[field][0];
    }
  }

  record(error) {
    this.error = error;
  }
}

export default {
  name: "CreateGroup",
  middleware: "auth",
  data: () => ({
    users: [],
    submitted: false,
    prevRoute: "",
    error: new error(),
    messages: false,
    group: {
      name: "",
      description: "",
      isVisible: 1,
      user: []
    },
    editor: ClassicEditor,
    editorConfig: {
      toolbar: [
        "heading",
        "|",
        "bold",
        "italic",
        "link",
        "bulletedList",
        "numberedList",
        "blockQuote"
      ],
      heading: {
        options: [
          {
            model: "paragraph",
            title: "Paragraph",
            class: "ck-heading_paragraph"
          },
          {
            model: "heading1",
            view: "h1",
            title: "Heading 1",
            class: "ck-heading_heading1"
          },
          {
            model: "heading2",
            view: "h2",
            title: "Heading 2",
            class: "ck-heading_heading2"
          },
          {
            model: "heading3",
            view: "h3",
            title: "Heading 3",
            class: "ck-heading_heading3"
          },
          {
            model: "heading4",
            view: "h4",
            title: "Heading 4",
            class: "ck-heading_heading4"
          }
        ]
      },
      placeholder: "Write a short bio..."
    },
    groupId: ""
  }),

  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (from.path != "/") {
        vm.prevRoute = from.path;
      } else {
        vm.prevRoute = "/home";
      }
    });
  },

  methods: {
    validationAddGroup() {
      this.submitted = true;
      let _this = this;
      this.$validator.validate().then(valid => {
        if (valid) {
          if (this.groupId == undefined) {
            axios
              .post(
                apiRoute + "/user/groups",
                this.group,
                this.$store.getters["auth/token"]
              )
              .then(response => {
                this.$router.push({ name: "group.index" });
              })
              .catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error);
              });
          } else {
            let data = {
              name: this.group.name,
              description: this.group.description,
              isVisible: 1,
              user: this.group.user
            };
            axios
              .put(
                apiRoute + "/user/groups/" + this.groupId,
                data,
                this.$store.getters["auth/token"]
              )
              .then(response => {
                this.$router.push({ name: "group.index" });
              })
              .catch(error => {
                let _this = this;
                _this.error.record(error.response.data.error);
              });
          }
        }
      });
    },

    getUser() {
      axios
        .get(apiRoute + "/users", this.$store.getters["auth/token"])
        .then(response => {
          let _this = this;
          response.data.data.forEach((value, key) => {
            _this.users.push({
              id: value.id,
              name: value.firstName + " " + value.lastName,
              avatarUri: value.avatarUri
            });
          });
        })
        .catch(error => {});
    },

    getGroup() {
      axios
        .get(
          apiRoute + "/user/groups/" + this.groupId,
          this.$store.getters["auth/token"]
        )
        .then(response => {
          let _this = this;
          _this.group = response.data.data;
          response.data.data.members.forEach((value, key) => {
            _this.group.user.push({
              id: value.id,
              name: value.firstName + " " + value.lastName,
              avatarUri: value.avatarUri
            });
          });
        })
        .catch(error => {});
    }
  },

  created() {
    this.groupId = this.$route.params.id;
  },

  mounted() {
    bus.$emit("overlay", 1);
    this.getUser();

    if (this.groupId) {
      this.getGroup();
    }
  }
};
</script>
<style lang="scss">
.members-select {
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
  .option-img-name {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 15px;
    }
    .name {
      display: block;
      font-size: 1.1rem;
      font-family: EncodeSansSemiBold;
    }
    .title {
      display: block;
      font-size: 1rem;
    }
  }
}
</style>

<style scoped lang="scss">
.create {
  background-color: #474747;
  @media (max-width: 767px) {
    padding-bottom: 60px;
  }
}
.create-white {
  padding-left: 20px;
  padding-right: 20px;
  background: #fff;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  .heading {
    font-family: EncodeSansSemiBold;
    font-size: 1.6rem;
    color: #474747;
  }
  .close-button {
    color: #474747;
    font-size: 1.6rem;
  }
}
input,
textarea {
  font-size: 1.2rem;
  color: #474747;
  border: none;
  background-color: #e5e5e5;
  &::placeholder {
    color: #474747;
  }
}
.action-buttons {
  background-color: #474747;
  text-align: center;
  padding-top: 20px;
  padding-bottom: 20px;
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
    @media (max-width: 767px) {
      padding: 5px 10px;
    }
    &:hover {
      opacity: 0.7;
      transition: 0.3s;
    }
  }
}
</style>
