/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/dropdown';
import $ from 'jquery';

const getNextIndex = function getNextIndex() {
  return $('#attributesContainer').attr('data-count');
};

const addAttributesNumber = function addAttributesNumber(number) {
  const currentIndex = parseInt(getNextIndex(), 10);
  $('#attributesContainer').attr('data-count', currentIndex + number);
};

const controlAttributesList = function controlAttributesList() {
  $('#attributesContainer .attribute').each((index, element) => {
    const value = $(element).attr('data-id');
    $('#sylius_product_attribute_choice').dropdown('set selected', value);
  });
};

const modifyAttributesListOnSelectorElementDelete = function modifyAttributesListOnSelectorElementDelete(removedValue) {
  $(`#attributesContainer [data-attributes_code="${removedValue}"]`).remove();
};

const modifySelectorOnAttributesListElementDelete = function modifySelectorOnAttributesListElementDelete() {
  $('[data-attributes_remove]').off('click').on('click', (event) => {
    const attributeId = $(event.currentTarget).parents('[data-attributes_code]').attr('data-attributes_code');

    $('div#attributeChoice > .ui.dropdown.search').dropdown('remove selected', attributeId);
    modifyAttributesListOnSelectorElementDelete(attributeId);
  });
};

const modifyAttributeFormElements = function modifyAttributeFormElements($response) {
  $response.find('input,select,textarea').each((index, element) => {
    if ($(element).attr('data-name') != null) {
      $(element).attr('name', $(element).attr('data-name'));
    }
  });

  return $response;
};

const isInTheAttributesContainer = function isInTheAttributesContainer(attributeId) {
  let result = false;
  $('#attributesContainer .attribute').each((index, element) => {
    const dataId = $(element).attr('data-id');
    if (dataId === attributeId) {
      result = true;
    }
  });

  return result;
};

const setAttributeChoiceListener = function setAttributeChoiceListener() {
  const $attributeChoice = $('#attributeChoice');
  $attributeChoice.find('button').on('click', (event) => {
    event.preventDefault();

    const $attributeChoiceSelect = $attributeChoice.find('select');
    let queryData = '';
    const $newAttributes = $attributeChoiceSelect.val();

    if ($newAttributes != null) {
      $attributeChoiceSelect.val().forEach((item) => {
        if (!isInTheAttributesContainer(item)) {
          queryData += `${$attributeChoiceSelect.prop('name')}=${item}&`;
        }
      });
    }
    queryData += `count=${getNextIndex()}`;

    $('form').addClass('loading');

    $.ajax({
      type: 'GET',
      url: $(event.currentTarget).parent().attr('data-action'),
      data: queryData,
      dataType: 'html',
      error() {
        $('form').removeClass('loading');
      },
      success(response) {
        const attributeFormElements = modifyAttributeFormElements($(response));

        attributeFormElements.each((index, element) => {
          $('#attributesContainer').append(element);
        });

        $('#sylius_product_attribute_choice').val('');

        addAttributesNumber($.grep(attributeFormElements, a => $(a).hasClass('attribute')).length);
        modifySelectorOnAttributesListElementDelete();

        $('form').removeClass('loading');
      },
    });
  });
};

const getAttributeInputNameSuffix = function getAttributeInputNameSuffix(name) {
  return name.substr(name.lastIndexOf('['), name.lastIndexOf(']'));
};

const applyAttributeValueToGroup = function applyAttributeValueToGroup() {
  $('body').on('click', '[data-attributes_apply]', (e) => {
    const current = $(e.currentTarget).parents('.attribute');
    const group = $(e.currentTarget).parents('.attribute-group').find('.attribute');

    group.each((i, item) => {
      $(':input:not([type=hidden])', item).val(function () {
        return $(`:input[name$="${getAttributeInputNameSuffix(this.name)}"]`, current).val();
      });
      $(':checkbox', item).prop('checked', function () {
        return $(`:checkbox[name$="${getAttributeInputNameSuffix(this.name)}"]`, current).prop('checked');
      });
    });
  });
};

$.fn.extend({
  productAttributes() {
    setAttributeChoiceListener();

    this.dropdown({
      onRemove(removedValue) {
        modifyAttributesListOnSelectorElementDelete(removedValue);
      },
      forceSelection: false,
    });

    controlAttributesList();
    modifySelectorOnAttributesListElementDelete();
    applyAttributeValueToGroup();
  },
});
