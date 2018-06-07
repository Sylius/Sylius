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
        bulkActionRequireConfirmation: function() {
            return this.each(function() {
                return $(this).on('click', function(event) {
                    event.preventDefault();

                    var actionButton = $(this);

                    if (actionButton.is('a')) {
                        $('#confirmation-button').attr('href', actionButton.attr('href'));
                    }

                    if (actionButton.is('button')) {
                        $('#confirmation-button').on('click', function(event) {
                            event.preventDefault();

                            var form = actionButton.closest('form');
                            $('input.bulk-select-checkbox:checked').each(function() {
                                $('<input type="hidden" name="ids[]" value="' + this.value + '">').appendTo(form);
                            });

                            return form.submit();
                        });
                    }

                    return $('#confirmation-modal').modal('show');
                });
            });
        }
    });
})( jQuery );
