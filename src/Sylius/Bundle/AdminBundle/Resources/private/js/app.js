/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/accordion';
import $ from 'jquery';
import 'jquery.dirtyforms/jquery.dirtyforms';

import 'sylius/ui/app';
import 'sylius/ui/sylius-auto-complete';
import 'sylius/ui/sylius-product-attributes';
import 'sylius/ui/sylius-product-auto-complete';
import 'sylius/ui/sylius-prototype-handler';

import './sylius-catalog-promotion-actions';
import './sylius-catalog-promotion-scopes';
import './sylius-compound-form-errors';
import './sylius-form-collection';
import './sylius-lazy-choice-tree';
import './sylius-menu-search';
import './sylius-move-product-variant';
import './sylius-move-taxon';
import './sylius-notification';
import './sylius-product-images-preview';
import './sylius-product-slug';
import './sylius-taxon-slug';

import StatisticsComponent from './sylius-statistics';
import SyliusTaxonomyTree from './sylius-taxon-tree';
import formsList from './sylius-forms-list';

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

  $('.sylius-update-product-variants').moveProductVariant($('.sylius-product-variant-position'));

  $('.sylius-taxon-move-up').taxonMove();
  $('.sylius-taxon-move-down').taxonMove();

  $('#sylius_shipping_method_calculator').handlePrototypes({
    prototypePrefix: 'sylius_shipping_method_calculator_calculators',
    containerSelector: '.configuration',
  }).change(() => {
    $('.ui.tabular.menu .item').tab();
  });
  setTimeout(() => {
    $('.ui.tabular.menu .item').tab();
  }, 50);

  $('#sylius_shipping_method_rules > a[data-form-collection="add"]').on('click', () => {
    setTimeout(() => {
      $('select[name^="sylius_shipping_method[rules]"][name$="[type]"]').last().change();
    }, 50);
  });

  $(document).setFromCollectionOnClickEventHandler('sylius_promotion_actions', 'actions');
  $(document).setFromCollectionOnClickEventHandler('sylius_promotion_rules', 'rules');

  $(document).on('collection-form-add', () => {
    $('.sylius-autocomplete').each((index, element) => {
      if ($._data($(element).get(0), 'events') == undefined) {
        $(element).autoComplete();
      }
    });

    if ($('#sylius_catalog_promotion_scopes').length > 0) {
      $(document).loadCatalogPromotionScopeConfiguration(
        document.querySelector('#sylius_catalog_promotion_scopes [data-form-collection="item"]:last-child')
      );
    }

    if ($('#sylius_catalog_promotion_actions').length > 0) {
      $(document).loadCatalogPromotionActionConfiguration(
        document.querySelector('#sylius_catalog_promotion_actions [data-form-collection="item"]:last-child')
      );
    }
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
  if ($('#sylius_catalog_promotion_actions').length > 0) {
    $(document).loadCatalogPromotionActionConfiguration(document.querySelector('#sylius_catalog_promotion_actions'));
  }
  if ($('#sylius_catalog_promotion_scopes').length > 0) {
    $(document).loadCatalogPromotionScopeConfiguration(document.querySelector('#sylius_catalog_promotion_scopes'));
  }

  $(document).previewUploadedImage('#add-avatar');

  const newNodeObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.addedNodes.length > 0) {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === Node.ELEMENT_NODE) {
            const formCollectionItem = node.closest('[data-form-collection="item"]');
            if (formCollectionItem) {
              if ($(formCollectionItem).find('.ui.accordion').length > 0) {
                $(formCollectionItem).find('.ui.accordion').accordion();
              }
              if ($(formCollectionItem).find('.ui.tabular.menu').length > 0) {
                $(formCollectionItem).find('.ui.tabular.menu .item').tab();
              }
            }
          }
        });
      }
    });
  });
  const observerConfig = {
    childList: true,
    subtree: true,
  };
  const targetNode = document.querySelector('body');
  newNodeObserver.observe(targetNode, observerConfig);

  const taxonomyTree = new SyliusTaxonomyTree();

  $(`${formsList}, .check-unsaved`).dirtyForms();

  $('#more-details').accordion({ exclusive: false });

  $('.variants-accordion__title').on('click', '.icon.button', function(e) {
    $(e.delegateTarget).next('.variants-accordion__content').toggle();
    $(this).find('.dropdown.icon').toggleClass('counterclockwise rotated');
  });

  const dashboardStatistics = new StatisticsComponent(document.querySelector('.stats'));

  $('.sylius-admin-menu').searchable('.sylius-admin-menu-search-input');
});

window.$ = $;
window.jQuery = $;
