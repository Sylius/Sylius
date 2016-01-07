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
    $.fn.resourceAutocomplete = function (options) {
        var settings = $.extend({
            src: "//"
        }, options);
        return this.attr({
            src: settings.src
        });
    };
})(jQuery);
