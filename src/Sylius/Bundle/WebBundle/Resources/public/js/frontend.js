/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require(["jquery", "jquery.bootstrap"], function ($) {
    'use strict';

    $(document).ready(function() {
        $('.sylius-different-billing-address-trigger').click(function() {
            $('#sylius-billing-address-container').toggleClass('hidden');
        });
    });
});
