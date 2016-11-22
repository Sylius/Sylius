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

    $.fn.extend({
        toggleElement: function() {
            return this.each(function() {
                $(this).on('change', function(event) {
                    event.preventDefault();

                    var toggleElement = $(this);
                    var targetElement = $('#' + toggleElement.data('toggles'));

                    if (toggleElement.is(':checked')) {
                        targetElement.show();
                    } else {
                        targetElement.hide();
                    }
                });

                return $(this).trigger('change');
            });
        }
    });
})( jQuery );
