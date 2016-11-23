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

    $.fn.extend({
        taxonMoveUp: function () {
            $(this).api({
                method: 'PUT',
                on: 'click',
                beforeSend: function (settings) {
                    settings.data = {
                        position: $(this).data('position') - 1
                    };

                    return settings;
                },
                onSuccess: function (response) {
                    location.reload();
                }
            });
        },
        taxonMoveDown: function () {
            $(this).api({
                method: 'PUT',
                on: 'click',
                beforeSend: function (settings) {
                    settings.data = {
                        position: $(this).data('position') + 1
                    };

                    return settings;
                },
                onSuccess: function (response) {
                    location.reload();
                }
            });
        }
    });
})(jQuery);
