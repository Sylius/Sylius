/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ($) {
    'use strict';

    $.fn.extend({
        productAttributes: function () {
            setAttributeChoiceListener();
            controlAttributesList();

            $(this).dropdown({
                onRemove: function(removedValue, removedText, $removedChoice) {
                    modifyAttributesListOnSelectorElementDelete(removedValue);
                }
            });

            modifySelectorOnAttributesListElementDelete();
        }
    });

    function addAttributesNumber(number) {
        var currentIndex = parseInt(getNextIndex());
        $('#attributesContainer').attr('data-count', currentIndex+number);
    }

    function getNextIndex() {
        return $('#attributesContainer').attr('data-count');
    }

    function controlAttributesList() {
        $('#attributesContainer .attribute').each(function() {
            var value = $(this).attr('data-id');
            $('[name="sylius_product_attribute_choice"]').dropdown('set selected', value);
        });
    }

    function modifyAttributesListOnSelectorElementDelete(removedValue) {
        $('#attributesContainer > .attribute[data-id="'+removedValue+'"]').remove();
    }

    function modifySelectorOnAttributesListElementDelete() {
        $('.attribute button').off('click').on('click', function() {
            var attributeId = $(this).parents('.attribute').attr('data-id');

            $('div#attributeChoice > .ui.dropdown.search').dropdown('remove selected', attributeId);
            $(this).parent().remove();
        });
    }

    function modifyAttributeForms(data) {
        $.each($(data).find('input,select,textarea'), function() {
            if ($(this).attr('data-name') != null) {
                $(this).attr('name', $(this).attr('data-name'));
            }
        });

        return data;
    }

    function setAttributeChoiceListener() {
        $('#attributeChoice button').on('click', function(event) {
            var $attributesContainer = $('#attributesContainer');
            event.preventDefault();

            var data = '';
            $(this).parent().find('select').val().forEach(function(item) {
                data += 'sylius_product_attribute_choice[]=' + item + "&";
            });
            data += "count=" + getNextIndex();

            $.ajax({
                type: 'GET',
                url: $(this).parent().attr('data-action'),
                data: data,
                dataType: 'html'
            }).done(function(data) {
                var finalData = modifyAttributeForms($(data));
                $attributesContainer.append(finalData);

                $('[name="sylius_product_attribute_choice"]').val('')

                addAttributesNumber($.grep($(finalData), function (a) { return $(a).hasClass('attribute'); }).length);
                modifySelectorOnAttributesListElementDelete();

                $('form').removeClass('loading');
            });
        });
    }
})( jQuery );
