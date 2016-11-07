(function ($) {
    'use strict';

    $(document).ready(function() {

        $('.sylius-delete-resource').api({
            method: 'delete',
            onSuccess: function (response) {
                var redirectUrl = $(this).data('success-redirect-url');

                if (redirectUrl) {
                    location.replace(redirectUrl);

                    return;
                }

                throw 'You need to define data-success-redirect-url on "' + $(this).attr('class') +'"';
            },
            onFailure: function (response) {
                var message = $(this).parent().parent().popup({
                    inline: true,
                    content: response.error.message,
                    on: 'manual'
                });
                message.popup('show');
                setTimeout(function() { message.popup('hide'); }, 3000);
            }
        });
    });
})( jQuery );
