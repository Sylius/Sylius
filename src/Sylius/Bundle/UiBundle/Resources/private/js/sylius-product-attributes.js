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

            $(this).dropdown({
                onRemove: function(removedValue, removedText, $removedChoice) {
                    modifyAttributesListOnSelectorElementDelete(removedValue);
                },
                forceSelection: false
            });

            controlAttributesList();
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
            $('#sylius_product_attribute_choice').dropdown('set selected', value);
        });
    }

    function modifyAttributesListOnSelectorElementDelete(removedValue) {
        $('#attributesContainer .attribute[data-id="'+removedValue+'"]').remove();
    }

    function modifySelectorOnAttributesListElementDelete() {
        $('.attribute button').off('click').on('click', function() {
            var attributeId = $(this).parents('.attribute').attr('data-id');

            $('div#attributeChoice > .ui.dropdown.search').dropdown('remove selected', attributeId);
            modifyAttributesListOnSelectorElementDelete(attributeId)
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
        var $attributeChoice = $('#attributeChoice');
        $attributeChoice.find('button').on('click', function(event) {
            event.preventDefault();

            var $attributeChoiceSelect = $attributeChoice.find('select');
            var data = '';
            var $newAttributes = $attributeChoiceSelect.val();

            if (null != $newAttributes) {
                $attributeChoiceSelect.val().forEach(function(item) {
                    if (!isInTheAttributesContainer(item)) {
                        data += $attributeChoiceSelect.prop('name') + '=' + item + "&";
                    }
                });
            }
            data += "count=" + getNextIndex();

            $.ajax({
                type: 'GET',
                url: $(this).parent().attr('data-action'),
                data: data,
                dataType: 'html',
                error: function() {
                    $('form').removeClass('loading');
                },
                success: function(data) {
                    var finalData = modifyAttributeForms($(data));

                    $(finalData).each(function() {
                        var localeCode = $(this).find('input[type="hidden"]').last().val();
                        $('#attributesContainer > div[data-tab="'+localeCode+'"]').append(this);
                    });

                    $('#sylius_product_attribute_choice').val('');

                    addAttributesNumber($.grep($(finalData), function (a) { return $(a).hasClass('attribute'); }).length);
                    modifySelectorOnAttributesListElementDelete();

                    $('form').removeClass('loading');
                }
            });
        });
    }

    function isInTheAttributesContainer(attributeId) {
        var result = false;
        $('#attributesContainer .attribute').each(function() {
            var dataId = $(this).attr('data-id');
            if (dataId === attributeId) {
                result = true;
            }
        });

        return result;
    }
})( jQuery );
