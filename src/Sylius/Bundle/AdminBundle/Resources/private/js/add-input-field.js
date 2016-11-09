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

    $('.button.position').on('click', function () {
        $(this)[0].parentNode.getElementsByClassName('sylius-product-taxon-position')[0].setAttribute('type', 'text');
        $(this).replaceWith("");
    })
}) (jQuery);
