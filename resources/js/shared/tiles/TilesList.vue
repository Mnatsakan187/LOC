
<script>
import TileProject from "./TileProject.vue";
import TileProfile from "./TileProfile.vue";
import TileEvent from "./TileEvent.vue";
import TileCollection from "./TileCollection.vue";

export default {
  name: "TilesList",
  props: ["tilesList"],
  components: {
    TileProject,
    TileProfile,
    TileEvent,
    TileCollection
  },
  render(createElement) {
    if (this.tilesList.length) {
      return createElement(
        "div",
        { class: "tiles" },
        this.tilesList.map(function(item) {
          switch (item.hudType) {
            case "project":
              return createElement(TileProject, {
                props: {
                  item: item
                }
              });
            case "event":
              return createElement(TileEvent, {
                props: {
                  item: item
                }
              });
            case "profile":
              return createElement(TileProfile, {
                props: {
                  item: item
                }
              });
            case "collection":
              return createElement(TileCollection, {
                props: {
                  item: item
                }
              });
          }
        })
      );
    } else {

    }
  }
};
</script>

<style lang="scss">
/* Tile styles start */
.tiles {
  padding: 20px 0;
  display: flex;
  flex-flow: row wrap;
  justify-content: flex-start;
  .tile {
    width: 290px;
    position: relative;
    margin-left: 1.6%;
    margin-bottom: 15px;
    cursor: pointer;
    @media (max-width: 2000px) {
      width: 18%;
      position: relative;
      margin-left: 1.6%;
      margin-bottom: 15px;
    }
    @media (max-width: 1140px) {
      width: 23%;
      margin-left: 1.6%;
      margin-bottom: 15px;
    }
    @media (max-width: 980px) {
      width: 30%;
      margin-left: 2.5%;
      margin-bottom: 15px;
    }
    @media (max-width: 720px) {
      width: 45%;
      margin-left: 3.3%;
      margin-bottom: 15px;
    }
    img {
      width: 100%;
      height: 100%;
      &.updated {
        -webkit-filter: grayscale(100%);
        filter: grayscale(100%);
      }
    }
    .info-dropdown {
      position: absolute;
      z-index: 1000;
      color: #fff;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      .info {
        font-family: "EncodeSansMedium";
        text-align-last: left;
        padding-top: 10px;
        padding-left: 15px;
        white-space: pre-wrap;
        word-wrap: break-word;
        white-space: normal;
        overflow-y: hidden;
        height: 80%;
        span {
          display: block;
          font-size: 1rem;
          &.title {
            font-family: "EncodeSansSemiBold";
            font-size: 1.3rem;
          }
          @media (max-width: 767px) {
            font-size: 0.9rem;
            &.title {
              font-size: 1rem;
            }
          }
        }
      }
    }

    &.tile-collection {
      .info-dropdown {
        .info {
          height: auto;
          width: 100%;
          text-align: center;
          text-align-last: center;
          padding-left: 0px;
          .updated-badge {
            color: #9d72ff;
            border: 2px solid #9d72ff;
            border-radius: 15px;
            padding: 5px 7px;
            display: inline-block;
          }
        }
      }
    }
    .follow-action-buttons {
      position: absolute;
      z-index: 1000;
      bottom: 5px;
      left: 10px;
      right: 10px;
      color: #fff;
      font-size: 1rem;
      .type-circle {
        text-transform: uppercase;
      }
      .action-area button {
        font-size: 1.2rem;
        text-decoration: none;
        background: none;
        border: none;
        color: #fff;
        .fas.fa-heart {
          color: #ff0000;
        }

      }
    }
    .overlay {
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      height: 100%;
      width: 100%;
      opacity: 0.6;
      &.w {
        background: rgb(1, 171, 131);
        background: linear-gradient(
            180deg,
            rgba(1, 171, 131, 1) 0%,
            rgba(0, 66, 51, 1) 100%
        );
      }
      &.a {
        background: rgb(164, 81, 163);
        background: linear-gradient(
            180deg,
            rgba(164, 81, 163, 1) 0%,
            rgba(42, 24, 42, 1) 100%
        );
      }
      &.v {
        background: rgb(1, 174, 255);
        background: linear-gradient(
            180deg,
            rgba(1, 174, 255, 1) 0%,
            rgba(0, 41, 60, 1) 100%
        );
      }
      &.e {
        background: rgb(140, 102, 228);
        background: linear-gradient(
            180deg,
            rgba(140, 102, 228, 1) 0%,
            rgba(23, 16, 37, 1) 100%
        );
      }
      &.g {
        background: rgb(68, 68, 68);
        background: linear-gradient(
            180deg,
            rgba(68, 68, 68, 1) 0%,
            rgba(0, 0, 0, 1) 100%
        );
      }
    }
    .action-dropdown {
      position: relative;
      .dd-toggle {
        color: #fff;
        z-index: 2000;
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
        z-index: 1990;
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
  }

  .isLike{
    color: red;
  }

  button:focus {
    outline: none;
  }


}
/* Tile styles end */
</style>
