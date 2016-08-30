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
        $('#sylius-product-adding-to-cart').submit({
            apiSettings: {
                redirectAction: 'cart',
                dataType: 'HTML',
                onError: function() {
                },
                onSuccess: function () {
                    window.location.replace(this.redicrectAction);
                }
            }
        })
    })
})( jQuery );
