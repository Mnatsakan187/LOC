<template>
    <div class="container-fluid detail detail-white-height">
      <div class="row detail-white">
        <div class="col">
          <div class="loc-navigation black text-center">
            <router-link class="btn" to="/analytics/followers"
              >My followers</router-link
            >
            <router-link class="btn" to="/analytics/projects"
              >My projects</router-link
            >
            <router-link class="btn" to="/analytics/polls"
              >My polls</router-link
            >
          </div>
        </div>
      </div>
      <div class="row detail-white">
        <div class="col">
          <router-view />
        </div>
      </div>
    </div>
</template>
<script>
export default {
  name: "Analytics",
  middleware: 'auth',
  data: function() {
    return {
      images: [],
      links: []
    };
  },
  mounted() {


    this.links = [
      {
        id: 0,
        url: "https://www.youtube.com/watch?v=SB-qEYVdvXA"
      },
      {
        id: 1,
        url: "https://www.youtube.com/watch?v=SB-qEYVdvXA"
      },
      {
        id: 2,
        url: "https://www.youtube.com/watch?v=SB-qEYVdvXA"
      }
    ];



    this.images = [
      {
        id: 0,
        link: "/images/guitar-post.jpeg"
      },
      {
        id: 0,
        link: "/images/guitar-post2.jpeg"
      }
    ];
  }
};
</script>

<style scoped lang="scss">
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

.loc-navigation.black .btn.router-link-exact-active {
  color: #4b4b4b;
}

.detail-white-height{
  background-color: white;
  height: 100%;
}
</style>

