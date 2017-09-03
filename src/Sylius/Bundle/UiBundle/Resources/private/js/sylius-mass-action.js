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
        massActionUrl: function() {
            return this.each(function() {
                return $(this).on('click', function(event) {
                    event.preventDefault();

                    // Get base URL
                    var url = $(this).attr('href');

                    // Get IDs from page
                    var ids = $('input.mass-select-checkbox:checked').map(function() {
                        return this.value;
                    }).get();

                    // Set URL to confirmation box
                    $('#confirmation-button').attr('href', [url, '?', $.param({ids: ids})].join(''));

                    // And open the confirmation box
                    return $('#confirmation-modal').modal('show');
                });
            });
        }
    });
})( jQuery );
