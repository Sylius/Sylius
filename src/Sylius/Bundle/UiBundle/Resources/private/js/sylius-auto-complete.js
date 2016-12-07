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
        autoComplete: function () {
            var element = $(this);

            element.dropdown({
                delay: {
                    search: 250,
                },
                apiSettings: {
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
                        var choiceName = element.data('choice-name');
                        var choiceValue = element.data('choice-value');
                        var myResults = [];
                        $.each(response._embedded.items, function (index, item) {
                            myResults.push({
                                name: item[choiceName],
                                value: item[choiceValue]
                            });
                        });

                        return {
                            success: true,
                            results: myResults
                        };
                    }
                }
            });
        }
    });
})( jQuery );
