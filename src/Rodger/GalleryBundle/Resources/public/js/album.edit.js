$.album_edit = function () {
    var $holder = $("#images-list-wrapper");
    var $images_upload_form = $("#images-upload-form");

    var $obj = {
        init: function () {
            $('#images-list', $holder).use_select_all();
            $images_upload_form.fileupload({
                url: $images_upload_form.attr('action')
            });
        }
    };

    $obj.init();
};

$(function () {
    $.album_edit()
});