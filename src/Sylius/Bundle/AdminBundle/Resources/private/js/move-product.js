(function ($) {
    'use strict';

    $(document).ready(function() {

        $('.sylius-product-index .ui.sortable.stackable.celled.table > tbody').sortable({
            forceFallback: true,
            onEnd: function (event) {
                $(this).api({
                    throttle: 500,
                    method: 'PUT',
                    action: 'move product taxon',
                    on: 'now',
                    urlData: {
                        id: $(event.item).find('input').data('id')
                    },
                    beforeSend: function (settings) {
                        settings.data = {
                            position: event.newIndex
                        };

                        return settings;
                    },
                    onFailure: function (response) {
                        throw 'Something went wrong with api call.';
                    }
                });
            }
        });

        $('.sylius-product-taxon-position').api({
            throttle: 500,
            method: 'PUT',
            beforeSend: function (settings) {
                settings.data = {
                    position: $(this).val()
                };
                return settings;
            },
            onSuccess: function (response) {
                location.reload();
            },
            onFailure: function (response) {
                throw 'Something went wrong with api call.';
            }
        });
        
    });

})( jQuery );
