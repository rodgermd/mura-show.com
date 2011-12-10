$.fn.use_select_all = function() {
  var $table = this,
      thead = $('thead', $table),
      tbody = $('tbody', $table),
      main_checkbox = $(':checkbox.checkbox-select-all', thead);

  var $obj = {
    init: function() {
      main_checkbox.click($obj.decide);
      $(':checkbox', tbody).change($obj.update_main);
      $obj.update_main();
    },
    decide: function() {
      main_checkbox.is(':checked') ? $obj.check() : $obj.uncheck();
    },
    check: function() {
      $(':checkbox', tbody).attr('checked', 'checked');
    },
    uncheck: function() {
      $(':checkbox', tbody).removeAttr('checked');
    },
    update_main: function() {
      ($(':checkbox', tbody).length == $(':checkbox:checked', tbody).length && $(':checkbox', tbody).length)
      ? main_checkbox.attr('checked', 'checked')
      : main_checkbox.removeAttr('checked');
    }
  };

  $obj.init();

  return this;
};


