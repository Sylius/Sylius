(function ( $ ) {
    'use strict';

    $.fn.extend({
        previewUploadedImage: function (root) {
            $(root + ' input[type="file"]').each(function() {
                $(this).change(function() {
                    displayUploadedImage(this);
                });
            });

            $(root + ' [data-form-collection="add"]').on('click', function() {
                var self = $(this);

                setTimeout(function() {
                    self.parent().find('.column:last-child input[type="file"]').on('change', function() {
                        displayUploadedImage(this);
                    });
                }, 500);
            });
        }
    });
})( jQuery );

function displayUploadedImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var image = $(input).parent().siblings('.image');

            if (image.length > 0) {
                image.attr('src', e.target.result);
            } else {
                var img = $('<img class="ui small bordered image"/>');
                img.attr('src', e.target.result);
                $(input).parent().before(img);
            }
        };

        reader.readAsDataURL(input.files[0]);
    }
}
