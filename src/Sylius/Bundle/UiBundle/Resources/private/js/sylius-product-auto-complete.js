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
        productAutoComplete: function () {
            $(this).each(function() {
                $(this).dropdown('set selected', $(this).find('input[name*="[associations]"]').val().split(','));
            });

            $(this).dropdown({
                delay: {
                    search: 250,
                },
                forceSelection: false,
                apiSettings: {
                    dataType: 'JSON',
                    cache: false,
                    data: {
                        criteria: { search: { type: 'contains', value: '' } }
                    },
                    beforeSend: function(settings) {
                        settings.data.criteria.search.value = settings.urlData.query;

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
                },
                onAdd: function(addedValue, addedText, $addedChoice) {
                    var inputAssociation = $addedChoice.parents('.product-select').find('input[name*="[associations]"]');
                    var associatedProductIds = 0 < inputAssociation.val().length ? inputAssociation.val().split(',') : [];

                    associatedProductIds.push(addedValue);
                    $.unique(associatedProductIds.sort());

                    inputAssociation.attr('value', associatedProductIds.join());
                },
                onRemove: function(removedValue, removedText, $removedChoice) {
                    var inputAssociation = $removedChoice.parents('.product-select').find('input[name*="[associations]"]');
                    var associatedProductIds = 0 < inputAssociation.val().length ? inputAssociation.val().split(',') : [];

                    associatedProductIds.splice($.inArray(removedValue, associatedProductIds), 1);

                    inputAssociation.attr('value', associatedProductIds.join());
                }
            });
        }
    });

})( jQuery );
