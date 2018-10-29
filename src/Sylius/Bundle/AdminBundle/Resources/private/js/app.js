/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/accordion';
import $ from 'jquery';

import 'sylius/ui/app';
import 'sylius/ui/sylius-auto-complete';
import 'sylius/ui/sylius-product-attributes';
import 'sylius/ui/sylius-product-auto-complete';
import 'sylius/ui/sylius-prototype-handler';

import './sylius-compound-form-errors';
import './sylius-lazy-choice-tree';
import './sylius-move-product';
import './sylius-move-product-variant';
import './sylius-move-taxon';
import './sylius-notification';
import './sylius-product-images-preview';
import './sylius-product-slug';
import './sylius-taxon-slug';

$(document).ready(() => {
  $('#sylius_product_variant_pricingCalculator').handlePrototypes({
    prototypePrefix: 'sylius_product_variant_pricingCalculator',
    containerSelector: '#sylius_calculator_container',
  });

  $('#sylius_customer_createUser').change(() => {
    $('#user-form').toggle();
  });

  $('.sylius-autocomplete').autoComplete();

  $('.product-select.ui.fluid.multiple.search.selection.dropdown').productAutoComplete();
  $('div#attributeChoice > .ui.dropdown.search').productAttributes();

  $('table thead th.sortable').on('click', (event) => {
    window.location = $(event.currentTarget).find('a').attr('href');
  });

  $('.sylius-update-product-taxons').moveProduct($('.sylius-product-taxon-position'));
  $('.sylius-update-product-variants').moveProductVariant($('.sylius-product-variant-position'));
  $('.sylius-taxon-move-up').taxonMoveUp();
  $('.sylius-taxon-move-down').taxonMoveDown();

  $('#sylius_shipping_method_calculator').handlePrototypes({
    prototypePrefix: 'sylius_shipping_method_calculator_calculators',
    containerSelector: '.configuration',
  });

  $('#actions a[data-form-collection="add"]').on('click', () => {
    setTimeout(() => {
      $('select[name^="sylius_promotion[actions]"][name$="[type]"]').last().change();
    }, 50);
  });
  $('#rules a[data-form-collection="add"]').on('click', () => {
    setTimeout(() => {
      $('select[name^="sylius_promotion[rules]"][name$="[type]"]').last().change();
    }, 50);
  });

  $(document).on('collection-form-add', () => {
    $('.sylius-autocomplete').each((index, element) => {
      if ($._data($(element).get(0), 'events') == undefined) {
        $(element).autoComplete();
      }
    });
  });
  $(document).on('collection-form-update', () => {
    $('.sylius-autocomplete').each((index, element) => {
      if ($._data($(element).get(0), 'events') == undefined) {
        $(element).autoComplete();
      }
    });
  });

  $('.sylius-tabular-form').addTabErrors();
  $('.ui.accordion').addAccordionErrors();
  $('#sylius-product-taxonomy-tree').choiceTree('productTaxon', true, 1);

  $(document).notification();
  $(document).productSlugGenerator();
  $(document).taxonSlugGenerator();

  $(document).previewUploadedImage('#sylius_product_images');
  $(document).previewUploadedImage('#sylius_taxon_images');

  $('body').on('DOMNodeInserted', '[data-form-collection="item"]', (event) => {
    if ($(event.target).find('.accordion').length > 0) {
      $(event.target).find('.accordion').accordion();
    }
  });
});

window.$ = $;
window.jQuery = $;
