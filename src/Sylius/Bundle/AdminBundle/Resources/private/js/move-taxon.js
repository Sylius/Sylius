(function ($) {
    'use strict';

    $(document).ready(function() {
        $('.sylius-taxon-move-up').api({
            method: 'PUT',
            on: 'click',
            beforeSend: function (settings) {
                settings.data = {
                    position: $(this).data('position') - 1
                };

                return settings;
            },
            onSuccess: function (response) {
                location.reload();
            }
        });

        $('.sylius-taxon-move-down').api({
            method: 'PUT',
            on: 'click',
            beforeSend: function (settings) {
                settings.data = {
                    position: $(this).data('position') + 1
                };

                return settings;
            },
            onSuccess: function (response) {
                location.reload();
            }
        });
    });
})( jQuery );
