
<script>
import PostBlock from "../../shared/newsfeed/PostBlock.vue";
import PollBlock from "../../shared/newsfeed/PollBlock.vue";
import ProjectBlock from "../../shared/newsfeed/ProjectBlock.vue";
import ImageBlock from "./ImageBlock.vue";
import TextBlock from "../../shared/newsfeed/TextBlock.vue";
import LinkBlock from "./LinkBlock.vue";

export default {
  name: "NewsFeed",
  props: ["newsList"],
  components: {
    PostBlock,
    PollBlock,
    ProjectBlock,
    ImageBlock,
    TextBlock,
    LinkBlock
  },
  render(createElement) {
    if (this.newsList.length) {
      // if(this.user.accountType == 1 ){
        return createElement(
          "div",
          { class: "news-feed" },
          this.newsList.map(function(item) {
            switch (item.hudType) {
              case "post":
                return createElement(PostBlock, {
                  props: {
                    item: item
                  }
                });
              case "poll":
                return createElement(PollBlock, {
                  props: {
                    item: item
                  }
                });
              case "project":
                return createElement(ProjectBlock, {
                  props: {
                    item: item
                  }
                });
              case "text":
                return createElement(TextBlock, {
                  props: {
                    item: item
                  }
              });
              case "image":
                return createElement(ImageBlock, {
                  props: {
                    item: item
                  }
              });
              case "link":
                return createElement(LinkBlock, {
                  props: {
                    item: item
                  }
              });
            }
          })
        );
      // }else{
      //   return createElement(
      //     "div",
      //     { class: "news-feed" },
      //     this.newsList.map(function(item) {
      //       switch (item.hudType) {
      //         case "post":
      //           return createElement(PostBlock, {
      //             props: {
      //               item: item
      //             }
      //         })
      //       }
      //     })
      //   );
      // }

    } else {
      return '';
    }
  },

  data: function() {
    return {
      user: {}
    };
  },

  methods:{
    getUser(){
      axios.get(apiRoute + '/user', this.$store.getters['auth/token']).then(response => {
        this.user = response.data.data;
      }).catch(error => {

      })
    }
  },

  created(){

  },

  mounted(){
    this.getUser();
  }
};
</script>

<style lang="scss">
.news-feed {
  max-width: 650px;
  @media (max-width: 575px) {
    padding: 0 20px;
  }
}
.news-action-dropdown {
  position: relative;
  .dd-toggle {
    color: #fff;
    z-index: 1031;
    position: relative;
    &.show {
      color: #000;
    }
  }
  .action-dropdown-menu {
    display: none;
    position: absolute;
    top: 0;
    right: 0;
    z-index: 1030;
    background-color: rgba(255, 255, 255, 0.8);
    width: 10rem;
    padding-top: 25px;
    a {
      display: block;
      color: #000;
      text-decoration: none;
      border-bottom: 1px solid #000;
      padding: 15px;
      font-family: EncodeSansMedium;
      font-size: 0.8rem;
      transition: 0.3s;
      &:last-child {
        border-bottom: none;
      }
      &:hover {
        color: #333;
        background-color: #fff;
        transition: 0.3s;
      }
    }
    &.show {
      display: block;
    }
  }
}

.isLike {
  color: red;
}
</style>
