<template>
    <div class="container-fluid initial">
      <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">LOC</a>

        <div class="navbar-nav ml-md-auto">
          <router-link class="nav-item nav-link" :to="{ name: 'login' }">Login</router-link>
          <span class="divider">|</span>
          <router-link class="nav-item nav-link" :to="{ name: 'register.user' }">Sign Up</router-link>
          <span class="divider">|</span>
          <router-link
            class="nav-item nav-link"
            :to="{ name: 'register.creator' }"
          >Sign Up As Creator</router-link>
        </div>
      </nav>
      <div class="row mt-5 align-items-center">
        <div class="col-md-12 col-lg-6 col-xl-7 text-center mt-3 order-lg-2">
          <img
            class="landing-1"
            src="/images/landing_1-700.png"
            srcset="/images/landing_1-974.png 974w, /images/landing_1-700.png 700w, /images/landing_1-450.png 450w"
            sizes="(min-width: 992px) 40vw, 90vw "
            alt="Landing 1"
          />
        </div>
        <div class="col-md-12 col-lg-6 col-xl-5 mt-5 mt-md-2 order-lg-1 mobile-center">
          <div
            class="title mt-3"
          >"Taking control of the online landing experience for your career gives the red carpet treatment to fans, followers, and industry professionals alike, and gives the data directly to you."</div>
          <div
            class="text mt-3"
          >Creators are storytellers. With so many platforms available for us all, how do we really know which story people are getting? It’s probably not the full one…</div>
          <div class="text stronger mt-3">It’s time our creativity had a new LOCation.</div>
          <router-link
            tag="button"
            class="btn mt-4"
            :to="{ name: 'register.creator' }"
          >Sign up as creator</router-link>
        </div>
      </div>
      <div class="row m-row align-items-center">
        <div class="col-6 text-center">
          <img class="landing-mobile" src="/images/landing_2.png" />
        </div>
        <div class="col-6">
          <div
            class="text stronger"
          >If people want to find your art, whatever it is, now they come directly to you.</div>
          <div class="dash mt-2">
            <img src="/images/dash.png" />
          </div>
        </div>
      </div>
      <div class="row m-row align-items-center">
        <div class="col-md-12 col-lg-6 col-xl-7 text-center mt-5 order-lg-2">
          <img class="landing-3" src="/images/landing_3.png" />
        </div>
        <div class="col-md-12 col-lg-6 col-xl-5 mt-5 mt-md-3 order-lg-1 mobile-center">
          <div
            class="text mt-3"
          >Fans engaging with you here, rather than over there, means this; you own your data. Use it individually, collaboratively, collectively.</div>
          <div class="text stronger mt-3">Your data, your choice.</div>
          <div class="dash mt-2">
            <img src="/images/dash.png" />
          </div>
        </div>
      </div>
      <div class="row m-row align-items-center">
        <div class="col-6 text-center">
          <img class="landing-mobile" src="/images/landing_4.png" />
        </div>
        <div class="col-6">
          <div class="text">
            The people who follow you on all of these platforms want to help you succeed. All of the algorithms and pay-per-post models make it hard.
            <br />
            <br />We think we’ve made it a whole lot easier;
          </div>
          <div class="dash mt-2">
            <img src="/images/dash.png" />
          </div>
        </div>
      </div>
      <div class="row m-row align-items-center">
        <div class="col-md-12 col-lg-6 col-xl-7 text-center mt-5 order-lg-2">
          <img class="landing-5" src="/images/landing_5.png" />
        </div>
        <div class="col-md-12 col-lg-6 col-xl-5 mt-5 mt-md-3 order-lg-1 mobile-center">
          <div class="text">
            All of our creative profiles and projects in one spot on a beautiful, efficient, and exciting platform.
            <br />
            <br />Loads of exclusive content that can’t be found anywhere else because it’s made right here, funded by the same money that’s currently going the way of those big tech companies.
            <br />
            <br />The opportunity for us to properly tell the stories of who we really are and what is really going on here, the way only creatives can.
          </div>
          <div class="dash mt-2">
            <img src="/images/dash.png" />
          </div>
        </div>
      </div>
      <div class="row align-items-top">
        <div class="col-12 col-md-6 mt-5 text-center">
          <router-link tag="button" class="btn" :to="{ name: 'register.user' }
           ">Sign up</router-link>
          <div class="text stronger mt-4">to change how you support your favourite creatives forever</div>
        </div>
        <div class="col-12 col-md-6 mt-5 text-center">
          <router-link
            tag="button"
            class="btn"
            :to="{ name: 'register.creator' }"
          >Sign up as creator</router-link>
          <div
            class="text stronger mt-4"
          >to roll out your own red carpet. It's time to claim your landing experience, and reclaim your data when you do.</div>
        </div>
      </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import LocaleDropdown from "../shared/LocaleDropdown";

export default {
  components: { LocaleDropdown },
  layout: "basic",
  middleware: "guest",

  metaInfo() {
    return { title: this.$t("home") };
  },

  data: () => ({
    title: window.config.appName
  }),

  computed: mapGetters({
    authenticated: "auth/check"
  })
};
</script>
<style scoped lang="scss">
.initial {
  background-image: url("/images/initial_page_background.png");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: top;
  padding-left: 100px;
  padding-right: 100px;
  color: #fff;
  font-size: 1.2rem;
  padding-bottom: 200px;
  @media (max-width: 1140px) {
    padding-left: 50px;
    padding-right: 50px;
  }
  @media (max-width: 700px) {
    padding-left: 20px;
    padding-right: 20px;
  }
  @media (max-width: 350px) {
    padding-left: 5px;
    padding-right: 5px;
  }
  @media (max-width: 991px) {
    background-image: url("/images/initial_page_background_medium.png");
  }
  @media (max-width: 400px) {
    background-image: url("/images/initial_page_background_small.png");
  }
  .navbar {
    max-width: 1800px;
    margin: 0 auto;
    @media (max-width: 450px) {
      justify-content: flex-start;
    }
    .navbar-nav {
      flex-direction: row !important;
    }
    .navbar-brand,
    .nav-item {
      padding-right: 0.5rem;
      padding-left: 0.5rem;
      color: #fff;
      transition: 0.3s;
      font-size: 1rem;
      @media (max-width: 450px) {
        font-size: 0.8rem;
      }
      &:hover {
        opacity: 0.5;
        transition: opacity 0.3s;
      }
    }
    .divider {
      margin-left: 10px;
      margin-top: 5px;
      margin-right: 5px;
    }
    @media (max-width: 450px) {
      width: 350px;
      .nav-item {
        padding-right: 0.1rem;
        padding-left: 0.1rem;
      }
      .divider {
        margin-left: 5px;
        margin-top: 3px;
      }
    }
  }
  .row {
    max-width: 1800px;
    margin: 0 auto;
  }
  .title {
    margin-top: 30px;
    font-size: 1.6rem;
    font-family: EncodeSansSemiBold;
    @media (max-width: 500px) {
      font-size: 1.4rem;
    }
  }
  .text.stronger {
    font-size: 1.4rem;
    font-family: EncodeSansMedium;
  }
  .mobile-center {
    @media (max-width: 991px) {
      text-align: center;
    }
  }
  img {
    &.landing-1 {
      width: 100%;
      max-width: 1000px;

      @media (max-width: 991px) {
        width: 60%;
      }
      @media (max-width: 500px) {
        width: 100%;
      }
    }
    &.landing-mobile {
      width: 50%;
      max-width: 350px;
      @media (max-width: 700px) {
        width: 70%;
      }
      @media (max-width: 500px) {
        width: 90%;
      }
    }
    &.landing-3 {
      width: 100%;
      max-width: 900px;
      @media (max-width: 991px) {
        width: 50%;
      }
      @media (max-width: 500px) {
        width: 100%;
      }
    }
    &.landing-5 {
      width: 100%;
      max-width: 800px;
      @media (max-width: 991px) {
        width: 60%;
      }
      @media (max-width: 500px) {
        width: 100%;
      }
    }
  }

  .btn {
    background: #fff;
    border-radius: 20px;
    color: #000;
    padding: 5px 15px;
    text-transform: uppercase;
    font-size: 1rem;
    transition: opacity 0.3s;
    &:hover {
      opacity: 0.4;
      transition: opacity 0.3s;
    }
  }

  .m-row {
    margin-top: 100px;
  }
}
</style>


