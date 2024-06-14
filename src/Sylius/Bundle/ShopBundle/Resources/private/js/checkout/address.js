document.addEventListener('DOMContentLoaded', () => {
  const shippingCheckbox = document.getElementById('sylius_checkout_address_differentShippingAddress');
  const shippingAddress = document.getElementById('sylius_checkout_shipping_address');
  const billingCheckbox = document.getElementById('sylius_checkout_address_differentBillingAddress');
  const billingAddress = document.getElementById('sylius_checkout_billing_address');

  if (shippingCheckbox && shippingAddress) {
    shippingCheckbox.addEventListener('change', () => {
      toggleAddress(shippingCheckbox, shippingAddress);
    });
  }

  if (billingCheckbox && billingAddress) {
    billingCheckbox.addEventListener('change', () => {
      toggleAddress(billingCheckbox, billingAddress);
    });
  }
});

function toggleAddress(checkbox, addressSection) {
  if (checkbox.checked) {
    addressSection.classList.remove('d-none');
  } else {
    addressSection.classList.add('d-none');
  }
}
