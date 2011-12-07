$(function(){
  if ($.isFunction($.fn.colorbox)) {
    $('.album-image a').colorbox({ 
      slideshow : true, 
      slideshowSpeed: 4000,
      slideshowAuto: false,
      loop: false,
      rel: 'show-in-colorbox', 
      transition:"elastic" });
  }
});