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
        $('.variant-table-toggle i.glyphicon').on('click', function(e) {
            $(this).toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
            $(this).parent().parent().find('table tbody').toggle();
        });
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    });
})( jQuery );
