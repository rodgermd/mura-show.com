$.album_edit = function() {
  var $holder = $("#images-list-wrapper");
  
  var $obj = {
    init: function() {
      $("#navigation a", $holder).live('click', $obj.load_page)
      $('#images-list', $holder).use_select_all();
      $('form', $holder).live('submit', $obj.submit_bulk_form)
    },
    load_page: function(e) {
      e.preventDefault();
      $obj.load($(this).attr('href'));
    },
    load: function(url) {
      $.ajax({
        url: url,
        success: function(r) {
          $holder.html($(r).html());
          $('#images-list', $holder).use_select_all();
        }
      });
    },
    submit_bulk_form: function() {
      var $form = $(this);
      $.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: $form.serialize(),
        statusCode: {
          200: function(r) { $holder.html($(r).html())},
          201: function() { $obj.load($('p.source', $holder).text()) }
        }
      });
      return false;
    }
    
  };
  
  $obj.init();
};

$(function(){$.album_edit()});