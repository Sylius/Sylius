/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        $('.variant-table-toggle .icon').toggle(function() {
            $(this).removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $(this).parent().parent().find('.table tbody').show();
        }, function() {
            $(this).addClass('icon-chevron-down').removeClass('icon-chevron-up');
            $(this).parent().parent().find('.table tbody').hide();
        });

        //$('select').select2();
    });
})( jQuery );
