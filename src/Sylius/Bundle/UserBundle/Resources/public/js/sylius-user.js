/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ($) {
    'use strict';

    $('#sylius_customer_create_user').change(function(){
        $('#user-form').toggle();
    });
})( jQuery );
