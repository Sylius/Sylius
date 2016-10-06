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

    $('table thead th.sortable').on('click', function () {
        window.location = $(this).find('a').attr('href');
    })
}) (jQuery);
