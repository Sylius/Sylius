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
  const $tabs = $('#sylius_product_variant_channelPricings div.ui.top.attached.tabular.menu a.item[data-disabled-channel-pricing]');
  const $segments = $('#sylius_product_variant_channelPricings div.ui.bottom.attached.tab.segment[data-disabled-channel-pricing]');
  if ($tabs.length !== $segments.length || $tabs.length === 0) {
    return;
  }

  // Tabs visibility
  $tabs.each(function () {
    $(this).toggle(toggle.is(':checked'));

    // Attached segment visibility symmetry
    const thisChannelCode = $(this).data('tab');
    $(`#sylius_product_variant_channelPricings div.ui.bottom.attached.tab.segment[data-disabled-channel-pricing][data-tab="${thisChannelCode}"]`)
      .attr('style', $(this).attr('style'));
  });

  // Active tab and segment after visibility update
  const $tabActive = $('#sylius_product_variant_channelPricings a.item.active');
  const $segmentActive = $('#sylius_product_variant_channelPricings div.ui.bottom.attached.tab.segment.active');
  if ($tabActive.is(':hidden')) {
    const $firstTabVisible = $('#sylius_product_variant_channelPricings a.item:visible').first();

    $tabActive.removeClass('active');
    $segmentActive.removeClass('active');
    $firstTabVisible.addClass('active');
    $(`#sylius_product_variant_channelPricings div.ui.bottom.attached.tab.segment[data-tab="${($firstTabVisible.data('tab'))}"]`)
      .addClass('active');
  }
};

$.fn.extend({
  toggleDisabledChannelPricings() {
    const $toggle = $('input[id="sylius_product_variant_showDisabledChannels"]');

    if ($toggle.length === 0) return;

    const toggleHandler = () => {
      const isChecked = $toggle.is(':checked');
      toggleConfigurableProduct(isChecked);
      toggleSimpleProduct(isChecked);
    };

    toggleHandler();
    $toggle.off('change').on('change', toggleHandler);
  },
});
