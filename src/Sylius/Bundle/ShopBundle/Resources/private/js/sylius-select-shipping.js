import 'semantic-ui-css/components/api';
import $ from 'jquery';

const handleShippingOptionChange = function handleShippingOptionChange() {
  const shippingPriceElement = $('#sylius-summary-shipping-total');
  const totalPriceElement = $('#sylius-summary-grand-total');

  const element = $('[name*="sylius_checkout_select_shipping[shipments][0][method]"]');
  const validationElement = $('#sylius-select-shipping-validation-error');

  element.api({
    method: 'GET',
    on: 'change',
    url: $(this).data('url'),
    onSuccess(response) {
      validationElement.addClass('hidden');
      const newSummary = $(response.content).find('#sylius-checkout-subtotal').parent().html();
      $('#sylius-checkout-subtotal').parent().html(newSummary);
    },
    onFailure() {
      $(validationElement).removeClass('hidden');
    },
  });
};

$.fn.extend({
  selectShipping() {
    if ($('#sylius-shipping-methods').length > 0) {
      handleShippingOptionChange();
    }
  },
});
