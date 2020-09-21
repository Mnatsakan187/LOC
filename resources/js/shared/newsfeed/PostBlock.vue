<template>
  <div class="block post-block mt-5">
    <div class="name-dropdown">
      <div class="date">{{dateChange(item.createdAt)}}</div>

      <div class="news-action-dropdown" v-click-outside="closeActionDropdown">
        <button
          class="btn dd-toggle"
          :class="{'show': collapsed}"
          v-on:click="toggleActionDropdown"
          type="button"
        >
          <i class="fas fa-ellipsis-h"></i>
        </button>
        <div class="action-dropdown-menu" :class="{'show': collapsed}">
          <router-link
            v-if="item.userId== item.userId"
            class="dd-item"
            :to="{ path: '/post/post-detail/'+item.id}"
          >View</router-link>
          <router-link
            v-if="user.id == item.userId"
            class="dd-item"
            :to="{ path: '/post/edit/'+item.id}"
          >Edit</router-link>
          <a
            v-if="user.id == item.userId"
            class="dd-item"
            @click="removePost(item.id, item.profileId)"
            v-on:click="showModal = true"
          >Delete</a>

          <a class="dd-item" @click="pinToTop(item.id, item.profileId)">Pin to top</a>
        </div>
      </div>
    </div>
    <div class="type">POST</div>

    <div class="image-container" v-if="item.images.length == 1">
      <img
        v-for="(image, key) in item.images"
        :src="'/storage/posts/mediaPostImage/media/' + image.id + '/' + image.uri"
      />
      <div class="created-by-color mt-4">
        Created By:
        <template>{{item.images[0].createdByImage}}</template>
      </div>
    </div>

    <div class="image-container" v-if="item.images.length == 0">
      <img src="/images/post.png" />
    </div>

    <!-- Image Slider-->
    <div class="slider-container" v-if="item.images.length > 1">
      <div id="carouselImages" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li
            v-for="(image, key) in item.images"
            v-bind:class="{'active': key == 0}"
            data-target="#carouselImages"
            :data-slide-to="key"
            class="active"
          ></li>
        </ol>
        <div class="carousel-inner">
          <div
            v-for="(image, key) in item.images"
            class="carousel-item"
            v-bind:class="{'active': key == 0}"
          >
            <div class="image-holder d-flex align-items-center justify-content-center">
              <img
                class="d-block"
                :src="'/storage/posts/mediaPostImage/media/' + image.id + '/' + image.uri"
                alt="First slide"
              />
            </div>
            <div class="created-by-color mt-4">
              Created By:
              <template>{{image.createdByImage}}</template>
            </div>
          </div>
        </div>
        <a class="carousel-control-prev" href="#carouselImages" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselImages" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
    </div>
    <div class="post-text mt-3" v-html="profileSummary"></div>

    <!--<span style="color: white; padding-top: 90px" v-if="item.createdBy">-->
    <!--Created By: {{item.createdBy}}-->
    <!--</span>-->

    <!--<span style="color: white; padding-top: 60px" v-for="profile in item.postProfiles">-->
    <!--{{profile.creativeTitle}}-->
    <!--</span>-->

    <!-- Post External Single Link -->
    <div class="link-container mt-5" v-if="item.links.length == 1 ">
      <iframe
        v-for="(link, key) in item.links"
        v-if="link.uri.includes('youtube') && link.mediaType == 5"
        height="400px"
        width="100%"
        :src="link.uri.replace('watch?v=', 'embed/')"
        frameborder="0"
        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      ></iframe>

      <iframe
        v-for="(link, key) in item.links"
        v-if="link.uri.includes('instagram')"
        width="100%"
        height="860px"
        :src="link.uri+'embed'"
        frameborder="0"
        allowfullscreen
      ></iframe>
    </div>
    <!-- Post External Link Slider-->
    <div class="slider-container mt-5" v-if="item.links.length > 1 ">
      <div id="carouselLinks" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li
            v-for="(link, key) in item.links"
            v-bind:class="{'active': key == 0}"
            data-target="#carouselLinks"
            :data-slide-to="key"
          ></li>
        </ol>
        <div class="carousel-inner">
          <div
            v-for="(link, key) in item.links"
            class="carousel-item"
            v-bind:class="{'active': key == 0}"
          >
            <iframe
              height="400px"
              v-if="link.uri.includes('youtube') && link.mediaType == 5"
              width="100%"
              :src="link.uri.replace('watch?v=', 'embed/')"
              frameborder="0"
              allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>

            <iframe
              v-if="link.uri.includes('instagram') && link.mediaType == 5"
              width="100%"
              height="860px"
              :src="link.uri+'embed'"
              frameborder="0"
              allowfullscreen
            ></iframe>
          </div>
        </div>
        <a class="carousel-control-prev" href="#carouselLinks" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselLinks" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
    </div>

    <div class="actions">
      <div class="likes-number">
        <i class="fas fa-heart isLike"></i>
        {{item.likeCount}}
      </div>
      <div class="action-buttons">
        <template v-if="routeName != 'group.view'">
          <template v-if="!liked.likeableId">
            <button
              class="btn"
              :disabled="disabled"
              v-if="!item.likes"
              @click="like(item.id, false, 0, 'posts')"
            >
              <i class="far fa-heart"></i>
            </button>
            <button
              class="btn"
              :disabled="disabled"
              v-else
              @click="like(item.id, true, item.likes.id, 'posts')"
            >
              <i class="fas fa-heart isLike"></i>
            </button>
          </template>

          <template v-else>
            <button
              class="btn"
              :disabled="disabled"
              v-if="liked.likeableId != item.id"
              @click="like(item.id, false, 0, 'posts')"
            >
              <i class="far fa-heart"></i>
            </button>
            <button
              class="btn"
              :disabled="disabled"
              v-else
              @click="like(item.id, true, liked.id, 'posts')"
            >
              <i class="fas fa-heart isLike"></i>
            </button>
          </template>
        </template>

        <template v-else>
          <button
            class="btn"
            :disabled="disabled"
            v-if="!item.likes"
            @click="like(item.id, false, 0, 'posts')"
          >
            <i class="far fa-heart"></i>
          </button>
          <button
            class="btn"
            :disabled="disabled"
            v-else
            @click="like(item.id, true, item.likes.id, 'posts')"
          >
            <i class="fas fa-heart isLike"></i>
          </button>
        </template>

        <social-sharing
          :url="facebookShareUrl+'/post/'+item.profileId+'/post-detail/'+item.id"
          :title="item.description"
          :description="item.description"
          :quote="item.description"
          :hashtags="item.description"
          :twitter-user="item.description"
          inline-template
          @close="close('posts', item.id,  item.shareCount+1)"
        >
          <network network="facebook">
            <button class="btn">
              <i class="fas fa-share"></i>
            </button>
          </network>
        </social-sharing>
      </div>
    </div>
    <ModalConfirmation v-if="showModal" :deleteId="postId" :type="type" :profileId="profileId">
      <div slot="body">Are you sure, you want to delete this post?</div>
    </ModalConfirmation>
  </div>
</template>

<script>
import ModalConfirmation from "../modals/ModalConfirmation";
export default {
  name: "PostBlock",
  components: { ModalConfirmation },
  props: ["item"],
  data() {
    return {
      collapsed: false,
      confirmData: Object,
      showModal: false,
      type: "",
      disabled: false,
      postId: "",
      profileId: "",
      facebookShareUrl: window.config.facebookShareUrl,
      user: {},
      liked: {},
      profileSummary: ""
    };
  },

  computed: {
    routeName() {
      return this.$route.name;
    }
  },

  methods: {
    toggleActionDropdown() {
      this.collapsed = !this.collapsed;
    },
    closeActionDropdown() {
      this.collapsed = false;
    },
    openModalConfirmation() {
      this.confirmData = {
        open: true,
        contentType: "post"
      };
      this.$root.$emit("confirmation modal", this.confirmData);
    },

    like(id, like, likeId, type) {
      if (this.$route.name != "group.view") {
        this.disabled = true;
        let _this = this;
        this.isActive = like;
        this.isActive = !this.isActive;
        if (this.isActive) {
          axios
            .post(
              apiRoute + "/user/" + type + "/" + id + "/likes",
              this.$store.getters["auth/token"]
            )
            .then(response => {
              this.disabled = false;
              this.liked = response.data.data;
              bus.$emit("like", 1);
            })
            .catch(error => {});
        } else {
          axios
            .delete(
              apiRoute + "/user/" + type + "/" + id + "/likes/" + likeId,
              this.$store.getters["auth/token"]
            )
            .then(response => {
              this.disabled = false;
              this.liked.likeableId = "deleteLiked";
              bus.$emit("like", 1);
            })
            .catch(error => {});
        }
      } else {
        this.disabled = true;
        let _this = this;
        this.isActive = like;
        this.isActive = !this.isActive;
        if (this.isActive) {
          axios
            .post(
              apiRoute + "/user/" + type + "/" + id + "/likes",
              this.$store.getters["auth/token"]
            )
            .then(response => {
              this.disabled = false;
              bus.$emit("like", 1);
            })
            .catch(error => {});
        } else {
          axios
            .delete(
              apiRoute + "/user/" + type + "/" + id + "/likes/" + likeId,
              this.$store.getters["auth/token"]
            )
            .then(response => {
              this.disabled = false;
              bus.$emit("like", 1);
            })
            .catch(error => {});
        }
      }
    },

    pinToTop(id, profileId) {
      axios
        .post(
          apiRoute + "/user/posts/pin-to-top",
          { id: id, profileId: profileId },
          this.$store.getters["auth/token"]
        )
        .then(response => {
          this.collapsed = false;
          bus.$emit("refresh", 1);
        })
        .catch(error => {});
    },

    close(type, id, shareCount) {
      let _this = this;
      if (type == "projects") {
        axios
          .post(
            apiRoute + "/user/" + type + "/" + id + "/share",
            { shareCount: shareCount },
            this.$store.getters["auth/token"]
          )
          .then(response => {
            _this.profileView();
          })
          .catch(error => {});
      } else {
        axios
          .post(
            apiRoute + "/user/" + type + "/" + id + "/share",
            { shareCount: shareCount },
            this.$store.getters["auth/token"]
          )
          .then(response => {
            _this.profileView();
          })
          .catch(error => {});
      }
    },

    removePost(postId, profileId) {
      this.postId = postId;
      this.profileId = profileId;
      this.type = "posts";
    },

    getUser() {
      let _this = this;
      axios
        .get(apiRoute + "/user", this.$store.getters["auth/token"])
        .then(response => {
          _this.user = response.data.data;
        })
        .catch(error => {});
    },

    dateChange: function(date) {
      return moment(date).format("MMM DD");
    }
  },
  created() {
    console.log(window.config.facebookShareUrl, 54646);

    bus.$on("closeConfDialog", data => {
      if (data == 1) {
        this.showModal = false;
      }
    });

    if (this.item.summary.includes("href=")) {
      let projectSum = this.item.summary.replace(
        'href="',
        'target="_blank" href='
      );
      this.profileSummary = projectSum.replace(/["']/g, "");
    } else {
      this.profileSummary = this.item.summary;
    }

    this.getUser();
  }
};
</script>

<style lang="scss">
.post-block {
  border-bottom: 1px solid #333;
  padding-bottom: 20px;
  .name-dropdown {
    display: flex;
    justify-content: space-between;
  }
  .post-name {
    font-size: 1.3rem;
    margin-bottom: 5px;
    font-family: EncodeSansSemiBold;
  }
  .type,
  .date {
    text-transform: uppercase;
    font-size: 0.9rem;
  }
  .post-image {
    img {
      max-width: 100%;
      max-height: 400px;
    }
  }
  .post-text {
    font-size: 1rem;
    text-align: justify;
  }
  .image-container {
    img {
      max-width: 100%;
      max-height: 400px;
    }
  }
  .slider-container {
    .carousel-indicators {
      top: 0;
      bottom: unset;
    }
    .image-holder {
      height: 400px;
      @media (max-width: 767px) {
        height: 300px;
      }
      img {
        margin: auto;
        max-width: 100%;
        max-height: 400px;
        @media (max-width: 767px) {
          max-height: 300px;
        }
      }
    }
  }
  .actions {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    .btn {
      color: #fff;
      font-size: 1.2rem;
      transition: 0.3s;
      &:hover {
        color: #9d72ff;
        transition: 0.3s;
      }
    }
  }
}

.created-by-color {
  color: white;
}
</style>


