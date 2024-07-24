/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

/**
 * Toggle visibility within the configurable product form.
 */
const toggleConfigurableProduct = (isChecked) => {
  const $segments = $('#sylius_product_variant_channelPricings .ui.segment[data-disabled-channel-pricing]:not(.bottom.attached.tab)');
  $segments.toggle(isChecked);
};

/**
 * Toggle visibility within the simple product form.
 */
const toggleSimpleProduct = (isChecked) => {
  const $channelPricings = $('#sylius_product_variant_channelPricings');
  const $tabs = $channelPricings.find('div.ui.top.attached.tabular.menu a.item[data-disabled-channel-pricing]');
  const $segments = $channelPricings.find('div.ui.bottom.attached.tab.segment[data-disabled-channel-pricing]');

  if ($tabs.length !== $segments.length || $tabs.length === 0) return;

  // Tabs and segments visibility
  $tabs.each(function () {
    const $tab = $(this);
    $tab.toggle(isChecked);

    // Synchronize visibility styles with the corresponding segment
    const channelCode = $tab.data('tab');
    $channelPricings.find(`div.ui.bottom.attached.tab.segment[data-disabled-channel-pricing][data-tab="${channelCode}"]`)
      .attr('style', $tab.attr('style'));
  });

  // Update the active tab and segment
  const $activeTab = $channelPricings.find('a.item.active');
  const $activeSegment = $channelPricings.find('div.ui.bottom.attached.tab.segment.active');

  if ($activeTab.is(':hidden')) {
    const $firstVisibleTab = $channelPricings.find('a.item:visible').first();

    $activeTab.removeClass('active');
    $activeSegment.removeClass('active');

    $firstVisibleTab.addClass('active');
    $channelPricings.find(`div.ui.bottom.attached.tab.segment[data-tab="${$firstVisibleTab.data('tab')}"]`)
      .addClass('active');
  }
};

$.fn.extend({
  toggleDisabledChannelPricings() {
    const $toggle = $('input[id="sylius_product_variant_showDisabledChannels"]');

    if ($toggle.length === 0) return;

    const toggleClicked = () => {
      const isChecked = $toggle.is(':checked');
      toggleConfigurableProduct(isChecked);
      toggleSimpleProduct(isChecked);
    };

    toggleClicked();
    $toggle.off('change').on('change', toggleClicked);
  },
});
