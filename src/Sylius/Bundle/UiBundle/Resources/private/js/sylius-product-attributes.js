/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
  // Once the enter key pressed on any field in the product page cause an attribute deletion.
  // When this bug occurs, the value of pageX is equal to 0. So if pageX is not equal to 0, it means the user clicked
  // on the delete button, so the remove method should be called.
  if (event.pageX != 0) {
    $(`#attributesContainer .attributes-group[data-attribute-code="${removedValue}"]`).remove();
  }
};

const modifySelectorOnAttributesListElementDelete = function modifySelectorOnAttributesListElementDelete() {
  $('.attributes-group button[data-attribute="delete"]').off('click').on('click', (event) => {
    const attributeId = $(event.currentTarget).parents('.attributes-group').attr('data-attribute-code');

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

const copyValueToAllLanguages = function copyValueToAllLanguages() {
  $('#attributesContainer').on('click', '.attribute [data-attribute="copy"]', (event) => {
    event.preventDefault();

    const $attributesContainer = $('#attributesContainer');
    const $masterAttribute = $(event.currentTarget).closest('.attribute');
    const attributeID = $masterAttribute.attr('data-id');
    const $attributeCollection = $attributesContainer.find(`[data-id="${attributeID}"]`);

    const $masterAttributeInputs = $masterAttribute.find('input:visible, select, textarea');

    $attributeCollection.each((index, attr) => {
      const $inputs = $(attr).find('input:visible, select, textarea');

      $inputs.each((i, input) => {
        if (input.getAttribute('type') === 'checkbox') {
          input.checked = $masterAttributeInputs[i].checked;
        } else if (input.nodeName === 'SELECT') {
          for (let x = 0; x < $inputs[i].length; x++) {
            const masterOption = Array.from($masterAttributeInputs[i].options).find((option) => option.value === input[x].value);
            input[x].selected = masterOption ? masterOption.selected : false;
          }
        } else {
          input.value = $masterAttributeInputs[i].value;
        }
      });
    });
  });
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
          const localeCode = $(element).find('input[type="hidden"]').last().val();
          $(`#attributesContainer > div`).append(element);
        });

        $('#sylius_product_attribute_choice').val('');

        addAttributesNumber(attributeFormElements.find('.attribute').length);
        modifySelectorOnAttributesListElementDelete();

        $('form').removeClass('loading');
      },
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
    copyValueToAllLanguages();
  },
});
