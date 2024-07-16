/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

$.fn.extend({
  toggleDisabledChannelPricings() {
    const toggleButton = $('input[name="sylius_product_variant[showDisabledChannels]"]');

    if (toggleButton.length === 0) {
      return;
    }

    $('#sylius_product_variant_channelPricings div[data-disabled-channel-pricing]').each(function() {
      if (toggleButton.is(':checked')) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });

    toggleButton.on('change', function() {
      $.fn.toggleDisabledChannelPricings()
    });
  },
});
