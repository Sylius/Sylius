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
            $(this).each(function () {
                var element = $(this);
                var criteriaType = $(this).data('criteria-type');
                var criteriaName = $(this).data('criteria-name');
                var remoteUrl = $(this).data('url');
                var choiceName = $(this).data('choice-name');
                var choiceValue = $(this).data('choice-value');
                var autocompleteValue = $(this).find('input.autocomplete').val();

                element.dropdown({
                    on: 'now',
                    delay: {
                        search: 250
                    },
                    forceSelection: false,
                    apiSettings: {
                        dataType: 'JSON',
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

                if (0 < autocompleteValue.split(',').length) {
                    var menuElement = element.find('div.menu');

                    menuElement.api({
                        on: 'now',
                        method: 'GET',
                        url: remoteUrl,
                        data: {
                            criteria: {}
                        },
                        beforeSend: function (settings) {
                            settings.data.criteria[choiceValue] = {type: 'in', value: '', limit: 1000};
                            settings.data.criteria[choiceValue].value = autocompleteValue;

                            return settings;
                        },
                        onSuccess: function (response) {
                            $.each(response._embedded.items, function (index, item) {
                                menuElement.append(
                                    $('<div class="item" data-value="'+item[choiceValue]+'">'+item[choiceName]+'</div>')
                                );
                            });
                        }
                    });
                }

                window.setTimeout(function () {
                    element.dropdown('set selected', element.find('input.autocomplete').val().split(','));
                }, 5000);
            });
        }
    });
})( jQuery );
