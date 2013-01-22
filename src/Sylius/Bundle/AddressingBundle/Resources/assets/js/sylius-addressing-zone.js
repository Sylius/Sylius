/*
 * This file is part of the Sylius sandbox application.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function ( $ ) {
    $(document).ready(function() {
        var typeSelect = $('#sylius_addressing_zone_type');

        $('form.form-horizontal').on('submit', function(e) {
            $('div[id^="sylius-addressing-zone-members-"]').not('[id$="'+ typeSelect.val() +'"]').each(function () {
                $(this).remove();
            });
        });

        typeSelect.on('change', function() {
            var value = $(this).val();
            $('div[id^="sylius-addressing-zone-members-"]').hide();
            $('#sylius-addressing-zone-members-' + value).show();
            $('.collection-add-btn').data('collection', 'sylius-addressing-zone-members-' + value);
        }).trigger('change');
    });
})( jQuery );
