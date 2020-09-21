<template>
  <div class="container-fluid detail">
    <div class="row detail-white">
      <div class="col-sm-12 col-md-8 col-xl-6 offset-md-2 offset-xl-3 mt-5 mb-5 limit-form-width">
        <!--Header-->
        <div class="header">
          <div class="heading">Post</div>
          <button @click="closePost" class="btn close-button">
            <i class="fas fa-times"></i>
          </button>
        </div>
        Content
        <div class="content mt-5">

          <div class="image-container" v-if="post.images && post.images.length == 1">
            <img :src="'/storage/posts/mediaPostImage/media/' + post.images[0].id + '/' + post.images[0].uri"/>
            <div  class="created-by-color mt-4">
              Created By:
              <template>
                {{post.images[0].createdByImage}}
              </template>
            </div>
          </div>

          <!-- Image Slider-->
          <div class="slider-container" v-if="post.images && post.images.length > 1">
            <div id="carouselImages" class="carousel slide" data-ride="carousel">
              <ol class="carousel-indicators">
                <li v-for="(image, key) in post.images" v-bind:class="{'active': key == 0}"
                    data-target="#carouselImages" :data-slide-to="key" class="active"></li>
              </ol>
              <div class="carousel-inner">
                <div v-for="(image, key) in post.images" class="carousel-item" v-bind:class="{'active': key == 0}">
                  <div class="image-holder d-flex align-items-center justify-content-center">
                    <img
                      class="d-block"
                      :src="'/storage/posts/mediaPostImage/media/' + image.id + '/' + image.uri"
                      alt="First slide"
                    />
                  </div>
                  <div class="created-by-color mt-4">
                    Created By:
                    <template>
                      {{image.createdByImage}}
                    </template>
                  </div>
                </div>
              </div>
              <a
                class="carousel-control-prev"
                href="#carouselImages"
                role="button"
                data-slide="prev"
              >
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a
                class="carousel-control-next"
                href="#carouselImages"
                role="button"
                data-slide="next"
              >
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
          </div>


          <!-- Post Message --->
          <div class="post-message mt-3" v-html="profileSummary">
          </div>
          <!-- Post External Single Link -->
          <div class="link-container mt-5" v-if="post.links && post.links.length == 1 ">
            <iframe height="400px" v-for="(link, key) in post.links"
                    v-if="link.uri.includes('youtube') && link.mediaType == 5"
                    width="100%"
                    :src="link.uri.replace('watch?v=', 'embed/')"
                    frameborder="0"
                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
            ></iframe>

            <iframe  v-for="(link, key) in post.links" v-if="link.uri.includes('instagram')"
                     width="100%" height="780px" :src="link.uri+'embed'" frameborder="0" allowfullscreen>
            </iframe>
          </div>
          <!-- Post External Link Slider-->
          <div class="slider-container mt-5" v-if="post.links && post.links.length > 1 ">
            <div id="carouselLinks" class="carousel slide" data-ride="carousel">
              <ol class="carousel-indicators">
                <li v-for="(link, key) in post.links" v-bind:class="{'active': key == 0}" data-target="#carouselLinks"
                    :data-slide-to="key"></li>
              </ol>
              <div class="carousel-inner">
                <div v-for="(link, key) in post.links"  class="carousel-item" v-bind:class="{'active': key == 0}">
                  <iframe height="400px" v-if="link.uri.includes('youtube') && link.mediaType == 5"
                          width="100%"
                          :src="link.uri.replace('watch?v=', 'embed/')"
                          frameborder="0"
                          allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                          allowfullscreen
                  ></iframe>

                  <iframe  v-if="link.uri.includes('instagram') && link.mediaType == 5"
                           width="100%" height="780px" :src="link.uri+'embed'" frameborder="0" allowfullscreen>
                  </iframe>
                </div>
              </div>
              <a
                class="carousel-control-prev"
                href="#carouselLinks"
                role="button"
                data-slide="prev"
              >
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a
                class="carousel-control-next"
                href="#carouselLinks"
                role="button"
                data-slide="next"
              >
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: "PostDetail",
    middleware: 'auth',
    data: function () {
      return {
        post: [],
        prevRoute: '',
        notificationId: '',
        profileSummary: ''
      };
    },

    beforeRouteEnter(to, from, next) {
      next(vm => {
        if (from.path != '/') {
          vm.prevRoute = from.path;
        } else {
          vm.prevRoute = '/home';
        }
      })
    },

    methods: {
      getPosts() {
        let _this = this;
        axios.get(apiRoute + '/user/posts/' + this.postId, this.$store.getters['auth/token']).then(response => {
          _this.post = response.data.data
          if(response.data.data.summary.includes('href=')){
            let projectSum = response.data.data.summary.replace('href="', 'target="_blank" href=');
            this.profileSummary = projectSum.replace(/["']/g, "")
          }else{
            this.profileSummary = response.data.data.summary;
          }
        }).catch(error => {

        })
      },

      updateNotification() {
        axios.get(apiRoute + '/notifications/read/' + this.notificationId, this.$store.getters['auth/token']).then(response => {

        }).catch(error => {

        })
      },

      closePost() {
        if (this.post.profileId) {
          this.$router.push({path: '/profile-detail/' + this.post.profileId})
        } else {
          this.$router.push({path: '/project-detail/project/' + this.post.projectId})
        }

      }
    },

    created() {
      this.postId = this.$route.params.postId
      // this.profileId = this.$route.params.profileId
      this.notificationId = this.$route.params.notificationId
      this.getPosts();
    },

    mounted() {
      $(".dropdown-menu-notification").removeClass('show');
      if (this.notificationId) {
        this.updateNotification();
        bus.$emit('notRefresh', 1)
      }
    }
  };
</script>

<style scoped lang="scss">
  .detail {
    height: 100%;
  }

  .detail-white {
    padding-left: 20px;
    padding-right: 20px;
    background: #fff;
    @media (max-width: 767px) {
      padding-bottom: 80px;
    }
  }

  .limit-form-width {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
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

  .post-message {
    color: #474747;
    text-align: justify;
    font-size: 1.2rem;
  }

  .detail-white {
    height: 1150px !important;
  }

  .image-container {
    padding-bottom: 10px;
  }

  .created-by-color{
    color: black;
  }
</style>
