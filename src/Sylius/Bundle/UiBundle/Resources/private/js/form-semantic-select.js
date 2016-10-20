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
        $('.ui.fluid.search.selection.dropdown').dropdown({
            delay: {
                search: 250,
            },
            apiSettings: {
                action: 'get taxons',
                dataType: 'JSON',
                cache: false,
                data: { 
                    criteria: { name: { type: 'contains', value: '' } }
                },
                beforeSend: function(settings) {
                    settings.data.criteria.name.value = settings.urlData.query;
                    return settings;
                },
                onResponse: function (response) {
                    var myResults = [];
                    $.each(response._embedded.items, function (index, item) {
                        myResults.push({
                            name: item.name,
                            value: item.id
                        });
                    });
                    return {
                        success: true,
                        results: myResults
                    };
                }
            }
        })
    })
})( jQuery );
