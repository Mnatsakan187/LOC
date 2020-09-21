<template>
  <div class="modal fade" id="modal-remove-profile" tabindex="-1" role="dialog"
       aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="header-section header-section-white">
          <a class="fa fa-times right pointer color-light-blue" data-dismiss="modal"></a>
          <h2>Are you sure you want to remove this profile </h2>
        </div>
        <input type="hidden" name="profileId" class="profileId" id="profileId">
        <div class="modal-footer text-center">
          <button type="button" class="btn btn-large btn-light-grey"
                  data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-large btn-red"
                  @click="deleteProfile()">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import Cookies from 'js-cookie'
    export default {
        name: "modal",
      data() {
        return {
          token: Cookies.get('token'),

        }
      },

      methods:{
        deleteProfile() {
          var id = $("#modal-remove-profile").find('#profileId').val();
          axios.delete(apiRoute+ '/user/profiles/'+ id).then(response => {
            $('#modal-remove-profile').modal('hide');
          }).catch(function (error) {

          });
        }
      }

    }
</script>

<style scoped>
  .modal-footer {
    background: #fafbfc;
    text-align: center;
    border-top: none;
    padding: 30px;
    border-radius: 0 0 10px 10px;
  }

  .modal-footer .btn-light-grey {
    color: #a1acb5;
  }
  .btn-red {
    background-color: #f69798;
  }
  .btn-large {
    padding: 13px 30px;
    color: white;
    font-weight: 600;
    font-size: 14px;
    border-radius: 5px;
  }

  .btn-large {
    padding: 13px 30px;
    color: white;
    font-weight: 600;
    font-size: 14px;
    border-radius: 5px;
  }

  .btn-light-grey {
    background-color: #e9edef;
  }

  .modal-footer {
    background: #fafbfc;
    text-align: center;
    border-top: none;
    padding: 30px;
    border-radius: 0 0 10px 10px;
  }

  .modal-content {
    border-radius: 10px;
  }

  .modal-dialog .header-section, .modal-dialog-wide .header-section {
    display: block;
  }

  .modal-content {
    border-radius: 5px;
    box-shadow: none;
    border: 0;
  }

  .header-section.header-section-white {
    background: white;
  }

  .header-section.header-section-white {
    background: white;
  }

  .header-section {
    background: #fafbfc;
    padding: 30px;
    text-align: center;
    border-radius: 10px 10px 0 0;
  }

  .modal-dialog .header-section h2, .modal-dialog-wide .header-section h2 {
    font-weight: 600;
    color: #4c5962;
  }

  .header-section {
    background: #fafbfc;
    padding: 30px;
    text-align: center;
    border-radius: 10px 10px 0 0;
  }

  .color-light-blue, .color-light-blue::before {
    color: #c5daea;
  }

  .color-light-blue, .color-light-blue::before {
    color: #c5daea;
  }

  .fa.pull-right, .timeline .timeline-post .comment-info .pull-right.action-link.action-remove, .pull-right.action-link.action-remove, .pull-right.popover-icon {
    margin-left: 0.3em;
  }

  .modal-dialog .header-section h1, .modal-dialog .header-section h2, .modal-dialog-wide .header-section h1, .modal-dialog-wide .header-section h2 {
    font-size: 16px;
    font-weight: 400;
    margin: 0;
    color: #77828b;
    width: 100%;
  }

  .text-center{
    margin: auto;
  }
</style>
