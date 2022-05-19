/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'fomantic-ui/dist/components/accordion';
import 'fomantic-ui/dist/components/checkbox';
import 'fomantic-ui/dist/components/dimmer';
import 'fomantic-ui/dist/components/dropdown';
import 'fomantic-ui/dist/components/rating';
import 'fomantic-ui/dist/components/sidebar';
import 'fomantic-ui/dist/components/tab';
import 'fomantic-ui/dist/components/transition';
import $ from 'jquery';

import './sylius-bulk-action-require-confirmation';
import './sylius-form-collection';
import './sylius-require-confirmation';
import './sylius-toggle';
import './sylius-check-all';

$(document).ready(() => {
  $('#sidebar').addClass('visible');
  $('#sidebar').sidebar('attach events', '#sidebar-toggle', 'toggle');
  $('#sidebar').sidebar('setting', {
    dimPage: false,
    closable: false,
  });

  $('.ui.checkbox').checkbox();
  $('.ui.accordion').accordion();
  $('.ui.menu .dropdown').dropdown({ action: 'hide' });
  $('.ui.inline.dropdown').dropdown();
  $('.link.ui.dropdown').dropdown({ action: 'hide' });
  $('.button.ui.dropdown').dropdown({ action: 'hide' });
  $('.ui.fluid.search.selection.ui.dropdown').dropdown();
  $('.ui.tabular.menu .item, .sylius-tabular-form .menu .item').tab();
  $('.ui.card .dimmable.image, .ui.cards .card .dimmable.image').dimmer({ on: 'hover' });
  $('.ui.rating').rating('disable');

  $('form.loadable button[type=submit]').on('click', (event) => {
    $(event.currentTarget).closest('form').addClass('loading');
  });
  $('.loadable.button').on('click', (event) => {
    $(event.currentTarget).addClass('loading');
  });
  $('.message .close').on('click', (event) => {
    $(event.currentTarget).closest('.message').transition('fade');
  });

  $('[data-requires-confirmation]').requireConfirmation();
  $('[data-bulk-action-requires-confirmation]').bulkActionRequireConfirmation();
  $('[data-toggles]').toggleElement();
  $('[data-js-bulk-checkboxes]').checkAll();

  $('.special.cards .image').dimmer({
    on: 'hover',
  });

  $('[data-form-type="collection"]').CollectionForm();

  $('[data-js-disable]').on('click', (e) => {
    const $current = $(e.currentTarget);
    $(document).find($current.attr('data-js-disable')).addClass('disabled');
  });
});
