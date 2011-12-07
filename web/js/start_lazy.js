$(function() {
  if ($.isFunction($.fn.lazyload)) {
    $('img.lazy').lazyload({threshold: 100});
  }
});