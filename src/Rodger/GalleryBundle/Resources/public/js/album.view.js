$(function () {
    if ($.isFunction($.fn.colorbox)) {
        $('.album-image a').colorbox({
            slideshow: true,
            slideshowSpeed: 4000,
            slideshowAuto: false,
            maxHeight: '100%',
            loop: false,
            rel: 'show-in-colorbox',
            href: function () {
                return $.colorbox.element().attr('target-image')
            },
            transition: "elastic"
        });

        if (location.hash.length) {
            var image_id = location.hash.replace(/^#/, '');
            $('.album-image a[name=' + image_id + ']').click();
        }
    }
});