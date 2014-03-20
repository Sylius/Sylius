/*
 * This file is part of the Sylius sandbox application.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        var typeSelect = $('#sylius_zone_type');

        $('form.form-horizontal').on('submit', function(e) {
            $('div[id^="sylius-zone-members-"]').not('[id$="'+ typeSelect.val() +'"]').each(function () {
                $(this).remove();
            });
        });

        typeSelect.on('change', function() {
            var value = $(this).val();
            $('div[id^="sylius-zone-members-"]').hide();
            $('#sylius-zone-members-' + value).show();
            $('a[data-collection-button="add"]')
                .data('collection', 'sylius-zone-members-' + value)
                .data('prototype', 'sylius-zone-members-' + value)
            ;
        }).trigger('change');
    });
})( jQuery );
