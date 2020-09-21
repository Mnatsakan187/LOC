<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <!--Search form for mobile -->
        <div class="d-block d-md-none">
          <!--<form class="form-inline">-->
            <div class="form-group mobile-search">
              <span class="fa fa-search search-icon"></span>
              <input
                type="text"
                class="form-control"
                placeholder="Search"
                id="search"
                v-model="search" @keyup="searchResult()"
              />
            </div>
          <!--</form>-->
        </div>
        <!-- Search filter buttons -->
        <div class="btn-group-toggle ml-3 mr-3" data-toggle="buttons">
          <label class="btn" @click="typeFilter('all')">
            <input type="radio" checked autocomplete="off" /> All
          </label>
          <label class="btn" @click="typeFilter(1)">
            <input type="radio"  autocomplete="off" /> Creators
          </label>
          <label class="btn" @click="typeFilter(2)">
            <input type="radio"  autocomplete="off" /> Projects
          </label>
        </div>
        <div class="search-content ml-3 mr-3">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "search",
    middleware: 'auth',
    data() {
      return {
        titleYourContent: "YOUR CONTENT",
        titleAllLoc: "ALL LOC",
        selected: '',
        search:'',
      };
    },

    methods: {
      typeFilter(el){
        bus.$emit('filters', el)
      },

      searchResult() {
        bus.$emit('search', this.search)
      },
    },

    created() {

    },

    mounted() {

      $( "#search" ).click(function() {
         $(this).css({"background-color": "#62666a", "color": "white"})
      });
    }
  };
</script>

<style lang="scss">
  .mobile-search {
    background-color: #fff;
    padding: 20px 0px;
    width: 100%;
    input {
      background-color: #fff;
      border: none;
      padding-left: 2.3rem;
      width: 100%;
      height: 1.8rem;
      &::placeholder {
        color: #989898;
      }
    }
    .search-icon {
      position: absolute;
      left: 15px;
      z-index: 2;
      display: block;
      width: 2.3rem;
      line-height: 1.8rem;
      text-align: center;
      pointer-events: none;
      color: #989898;
    }
  }
  .btn-group-toggle {
    margin-top: 10px;
    /* @media (max-width: 767px) {
      text-align: center;
    }
    @media (max-width: 500px) {
      margin-top: 0px;
    }*/
    .btn {
      color: #c1c1c1;
      background-color: #000;
      border: 1px solid #c1c1c1;
      border-radius: 20px;
      margin-right: 10px;
      padding: 10px 15px;
      &.active {
        background-color: #545454;
        border: 1px solid #545454;
      }
      /*@media (max-width: 500px) {
        margin-right: 5px;
        padding: 5px 9px;
        font-size: 1rem;
      }*/
    }
  }
  .search-content {
    margin-top: 30px;
    .title {
      color: #949494;
    }
    .hud-search-divider {
      margin-top: 30px;
      margin-bottom: 30px;
      border-top: 1px solid #656565;
    }
    ul.search-results {
      list-style: none;
      padding-left: 0px;
      li {
        padding: 10px 0;
        a {
          text-decoration: none;
          color: #fff;
          transition: 0.3s;
          font-family: EncodeSansRegular;
          font-size: 1rem;
          span {
            display: block;
            &.bigger {
              font-size: 1.2rem;
            }
          }
          &:hover {
            color: #9d72ff;
            transition: 0.3s;
          }
        }
      }
    }
    .link-see-all {
      display: inline-block;
      text-decoration: none;
      color: #c1c1c1;
      border: 1px solid #c1c1c1;
      border-radius: 20px;
      padding: 10px 15px;
      text-transform: uppercase;
      transition: 0.3s;
      i {
        margin-left: 15px;
      }
      &:hover {
        text-decoration: none;
        color: #9d72ff;
        border: 1px solid #9d72ff;
        transition: 0.3s;
      }
    }
    .nothing-found {
      margin: 30px 0;
      text-align: center;
      i {
        font-size: 1.8rem;
        color: #fff;
        margin-bottom: 10px;
      }
      span {
        display: block;
        color: #fff;
        font-size: 1.2rem;
      }
    }
  }

  .mobile-search{
    background-color: #62666a;
  }

  .mobile-search input{
    background-color: #62666a;
  }
</style>
