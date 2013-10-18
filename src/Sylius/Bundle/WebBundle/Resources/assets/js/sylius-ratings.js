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
    $(document).ready(function () {

        var imagePath = '/bundles/syliusweb/img/raty';

        //set up static rating displays
        $('.rating').empty().removeClass('badge').raty({
            path: imagePath,
            readOnly: true,
            score: function() {
                return $(this).data('score');
            }
        });

        //set up rating widgets
        $('.rating-widget').each(function (){
            var $el = $(this);
            $('<span></span>').raty({
                score: $el.val(),
                path: imagePath,
                target: '#'+$el.attr('id'),
                targetType: 'number',
                targetKeep: true
            }).insertBefore($el);
            $el.hide();
        });
    });
})(jQuery);
