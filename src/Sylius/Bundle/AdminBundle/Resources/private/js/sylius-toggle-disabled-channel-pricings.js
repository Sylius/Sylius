/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

const toggleConfigurableProduct = function toggleConfigurableProduct(toggle) {
  $('#sylius_product_variant_channelPricings .ui.segment[data-disabled-channel-pricing]:not(.bottom.attached.tab)')
    .toggle(toggle.is(':checked'));
};

const toggleSimpleProduct = function toggleSimpleProduct(toggle) {
  const $tabs = $('#sylius_product_variant_channelPricings .ui.top.attached.tabular.menu .item[data-disabled-channel-pricing]');
  const $segments = $('#sylius_product_variant_channelPricings .ui.bottom.attached.tab.segment[data-disabled-channel-pricing]');

  if ($tabs.length !== $segments.length || $tabs.length === 0) {
    return;
  }

  // Tabs visibility
  $tabs.each(function () {
    $(this).toggle(toggle.is(':checked'));

    // Attached segment visibility symmetry
    const dataTab = $(this).data('tab');
    const segment = $(`#sylius_product_variant_channelPricings div.ui.bottom.attached.tab.segment[data-disabled-channel-pricing][data-tab="${dataTab}"]`);
    segment.attr('style', $(this).attr('style'));
  });

  // Handle active tab-segment
  const $activeTab = $('#sylius_product_variant_channelPricings a.item.active');
  const $activeSegment = $(`#sylius_product_variant_channelPricings div.ui.bottom.attached.tab.segment[data-disabled-channel-pricing][data-tab="${($activeTab.data('tab'))}"]`);
  if ($activeTab.is(':hidden')) {
    $activeTab.removeClass('active');
    $activeSegment.removeClass('active');

    const $firstVisibleTab = $('#sylius_product_variant_channelPricings a.item:visible').first();
    const $firstSegment = $(`div.ui.bottom.attached.tab.segment[data-tab="${($firstVisibleTab.data('tab'))}"]`);
    $firstVisibleTab.addClass('active');
    $firstSegment.addClass('active');
  }
};

$.fn.extend({
  toggleDisabledChannelPricings() {
    const toggle = $('input[id="sylius_product_variant_showDisabledChannels"]');
    if (toggle.length === 0) {
      return;
    }

    // Show disabled channels for a simple product
    toggleConfigurableProduct(toggle);

    // Show disabled channels for a configurable product
    toggleSimpleProduct(toggle);

    toggle.off('change').on('change', function () {
      $.fn.toggleDisabledChannelPricings();
    });
  },
});
