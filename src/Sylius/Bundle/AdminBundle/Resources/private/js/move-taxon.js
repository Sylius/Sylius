(function ($) {
    'use strict';

    $(document).ready(function() {

        $('.sylius-sortable-list').sortable({
            forceFallback: true,
            onEnd: function (event) {
                $(this).api({
                    throttle: 500,
                    method: 'PUT',
                    action: 'move taxon',
                    on: 'now',
                    urlData: {
                        id: $(event.item).data('id')
                    },
                    beforeSend: function (settings) {
                        settings.data = {
                            position: event.newIndex
                        };

                        return settings;
                    },
                    onSuccess: function (response) {
                        $(event.item).find('.sylius-taxon-move-up').data('position', event.newIndex);
                        $(event.item).find('.sylius-taxon-move-down').data('position', event.newIndex);
                    },
                    onFailure: function (response) {
                        throw 'Something went wrong with api call.';
                    }
                });
            }
        });

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
