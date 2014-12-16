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

    $(document).ready(function() {
        $('.variant-table-toggle i.glyphicon').on('click', function(e) {
            $(this).toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
            $(this).parent().parent().find('table tbody').toggle();
        });

        $('.datepicker').datepicker({});

        $('.available-on-demand-toggle').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            var value = $this.data('value');
            var url = $this.attr('href');

            $.ajax(url, {
                'type': 'PUT',
                'data': value ? {} : {'availableOnDemand': !value},
                'headers': {
                    'Accept': "application/json; charset=utf-8",
                },
                'success': function () {
                    if (value) {
                        $this.data('value', false);
                        $this.html('<i class="glyphicon glyphicon-remove-sign" style="color: #d9534f;"></i>');
                    } else {
                        $this.data('value', true);
                        $this.html('<i class="glyphicon glyphicon-ok-sign" style="color: #1abb9c;"></i>');
                    }
                },
                'error': function () {
                    alert('Error!');
                }
            });

        });
    });
})( jQuery );
