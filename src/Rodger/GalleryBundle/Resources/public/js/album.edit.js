$.album_edit = function () {
  var $holder = $("#images-list-wrapper");
  var $images_upload_form = $("#images-upload-form");

  var $obj = {
    init: function () {
      $("#navigation a", $holder).live('click', $obj.load_page)
      $('#images-list', $holder).use_select_all();
      $images_upload_form.fileupload({
        url: $images_upload_form.attr('action')
      });
    },
    load_page: function (e) {
      e.preventDefault();
      $obj.load($(this).attr('href'));
    },
    load: function (url) {
      $.ajax({
        url: url,
        success: function (r) {
          $holder.html($(r).html());
          $('#images-list', $holder).use_select_all();
        }
      });
    }

  };

  $obj.init();
};

$(function () {
  $.album_edit()
});