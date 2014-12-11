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
        if (0 == $('[data-form-collection="item"]').length) {
            $('#sylius_zone_type').trigger('change');
        }
    });
})( jQuery );
