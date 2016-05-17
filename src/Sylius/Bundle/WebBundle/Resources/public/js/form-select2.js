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
        $('input[class^="autocomplete"]').select2({
            ajax: {
                dataType: "json",
                minimumInputLength: 2,
                quietMillis: 250,
                url: $('input[class^="autocomplete"]').attr('src'),
                data: function (term) {
                    return {
                        criteria: { name: {type: 'contains', value: term} }
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data._embedded.items, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.name
                        });
                    });
                    return {
                        results: myResults
                    };
                }
            }
        });
    });
})( jQuery );
