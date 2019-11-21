import $ from 'jquery';

const getPriceFromString = (str) => {
  const arr = str.match(/(\D+)([\d.]+)/);
  if (!arr.length) {
    return null;
  }

  return [
    arr[1],
    arr[2].includes('.') ? arr[2].split('.')[1].length : 0,
    parseFloat(arr[2]),
  ];
};

const handleShippingOptionChange = function handleShippingOptionChange() {
  const shippingPriceElement = $('#sylius-summary-shipping-total');
  const totalPriceElement = $('#sylius-summary-grand-total');

  $('[name*="sylius_checkout_select_shipping[shipments][0][method]"]').on('change', (event) => {
    const newShippingPriceStr = $(event.currentTarget)
      .parents('.item')
      .find('.fee')
      .text()
      .trim();

    const [currency, decimalPlaces, newShippingPrice] = getPriceFromString(newShippingPriceStr);
    const [, , shippingPrice] = getPriceFromString(shippingPriceElement.text().trim());
    let [, , totalPrice] = getPriceFromString(totalPriceElement.text().trim());
    totalPrice -= (shippingPrice - newShippingPrice);
    shippingPriceElement.text(currency + newShippingPrice.toFixed(decimalPlaces));
    totalPriceElement.text(currency + totalPrice.toFixed(decimalPlaces));
  });
};

$.fn.extend({
  selectShipping() {
    if ($('#sylius-shipping-methods').length > 0) {
      handleShippingOptionChange();
    }
  },
});
