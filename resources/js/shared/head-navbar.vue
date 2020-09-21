<template>
  <div class="nav">
    <input type="checkbox" id="nav-check">
    <div class="nav-header">
      <div class="nav-title">
      </div>
    </div>
    <div class="nav-btn">
      <label for="nav-check">
        <span></span>
        <span></span>
        <span></span>
      </label>
    </div>
    <div class="nav-links">
      <a href="javascript:void(0)" class="add"><i class="fas fa-plus-circle"></i></a>
      <a href="javascript:void(0)"><i class="fas fa-calendar"></i></a>
      <a href="javascript:void(0)"><i class="fas fa-envelope"></i></a>
      <a href="javascript:void(0)"><i class="fas fa-bell"></i></a>
      <a href="javascript:void(0)">
        <img src="/images/user8-128x128.jpg" class="user-image" alt="User Image">
        <span class="user-data hidden-xs">Paul Smith</span>
      </a>
      <a href="javascript:void(0)">
        <locale-dropdown></locale-dropdown>
      </a>
    </div>
  </div>
</template>

<script>
    import LocaleDropdown from "./LocaleDropdown";
    export default {
        name: "head-navbar",
      components: {LocaleDropdown}
    }
</script>

<style scoped>
  .nav {
    position: absolute;
    height: 50px;
    width: 100%;
    background-color: #ffffff;
    display: block;
    z-index: 1;
  }


  .nav-links a i {
    color: #949494;
  }

  .add{
    margin-right: 30px;
  }

  .add i {
    font-size: 23px;
    color: #199ed8 !important;
  }

  .user-image {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 10px;
    margin-top: -2px;
  }

  .user-data{
    font-size: 12px;
    color: black;

  }

  .nav > .nav-header {
    display: inline;
  }

  .nav > .nav-header > .nav-title {
    display: inline-block;
    font-size: 22px;
    color: #fff;
    padding: 10px 10px 10px 10px;
  }

  .nav > .nav-btn {
    display: none;
  }

  .nav > .nav-links {
    display: inline;
    float: right;
    font-size: 18px;
  }

  .nav > .nav-links > a {
    display: inline-block;
    padding: 13px 10px 13px 10px;
    text-decoration: none;
    color: #efefef;
  }

  .nav > .nav-links > a:hover {
    background: #656565;
    color: #FFFF;
  }

  .nav > #nav-check {
    display: none;
  }

  .nav-item-language {
    position: relative !important;
  }

  @media (max-width:600px) {
    .nav > .nav-btn {
      display: inline-block;
      position: absolute;
      right: 0px;
      top: 0px;
    }
    .nav > .nav-btn > label {
      display: inline-block;
      width: 50px;
      height: 50px;
      padding: 13px;
    }
    .nav > .nav-btn > label:hover,.nav  #nav-check:checked ~ .nav-btn > label {
      background-color: rgba(0, 0, 0, 0.3);
    }
    .nav > .nav-btn > label > span {
      display: block;
      width: 25px;
      height: 10px;
      border-top: 2px solid #eee;
    }
    .nav > .nav-links {
      position: absolute;
      display: block;
      width: 100%;
      background-color: #333;
      height: 0px;
      transition: all 0.3s ease-in;
      overflow-y: hidden;
      top: 50px;
      left: 0px;
    }
    .nav > .nav-links > a {
      display: block;
      width: 100%;
    }
    .nav > #nav-check:not(:checked) ~ .nav-links {
      height: 0px;
    }
    .nav > #nav-check:checked ~ .nav-links {
      height: calc(100vh - 50px);
      overflow-y: auto;
    }
  }
</style>
