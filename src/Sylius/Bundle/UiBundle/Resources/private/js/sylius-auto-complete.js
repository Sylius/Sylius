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
                    var criteriaName = $(this).data('criteria-name');
                    var choiceName = $(this).data('choice-name');
                    var choiceValue = $(this).data('choice-value');
                    var autocompleteValue = $(this).find('input.autocomplete').val();
                    var loadForEditUrl = $(this).data('load-edit-url');

                    element.dropdown({
                        delay: {
                            search: 250
                        },
                        forceSelection: false,
                        apiSettings: {
                            dataType: 'JSON',
                            cache: false,
                            beforeSend: function(settings) {
                                settings.data[criteriaName] = settings.urlData.query;

                                return settings;
                            },
                            onResponse: function (response) {
                                var choiceName = element.data('choice-name');
                                var choiceValue = element.data('choice-value');
                                var myResults = [];
                                $.each(response, function (index, item) {
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
                            url: loadForEditUrl,
                            beforeSend: function (settings) {
                                settings.data[choiceValue] = autocompleteValue.split(',');

                                return settings;
                            },
                            onSuccess: function (response) {
                                $.each(response, function (index, item) {
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
