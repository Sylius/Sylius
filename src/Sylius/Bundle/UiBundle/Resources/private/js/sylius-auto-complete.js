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
            var criteriaType = $(this).data('criteria-type');
            var criteriaName = $(this).data('criteria-name');

            element.dropdown({
                delay: {
                    search: 250
                },
                forceSelection: false,
                apiSettings: {
                    dataType: 'JSON',
                    cache: false,
                    data: {
                        criteria: {}
                    },
                    beforeSend: function(settings) {
                        settings.data.criteria[criteriaName] = {type: criteriaType, value: ''};
                        settings.data.criteria[criteriaName].value = settings.urlData.query;

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
