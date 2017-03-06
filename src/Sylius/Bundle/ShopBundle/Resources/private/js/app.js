/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


(function($) {
    $(document).ready(function () {
        $('.cart.button')
            .popup({
                popup: $('.cart.popup'),
                on: 'click',
            })
        ;

        $('.star.rating').rating({
            fireOnInit: true,
            onRate: function(value) {
                $("[name='sylius_product_review[rating]']:checked").removeAttr('checked');
                $("#sylius_product_review_rating_"+(value-1)).attr('checked', 'checked');
            }
        });

        $('#sylius_checkout_address_customer_email').apiToggle({
            dataType: 'json',
            method: 'GET',
            throttle: 1500,

            beforeSend: function (settings) {
                settings.data = {
                    email: $('#sylius_checkout_address_customer_email').val()
                };

                return settings;
            },

            successTest: function (response) {
                return $('#sylius_checkout_address_customer_email').val() === response.username;
            }
        }, $('#sylius-api-login-form'));

        $('#sylius-api-login').apiLogin({
            method: 'POST',
            throttle: 500
        });

        $('.sylius-cart-remove-button').removeFromCart();
        $('#sylius-product-adding-to-cart').addToCart();

        $('#sylius-shipping-address').addressBook();
        $('#sylius-billing-address').addressBook();
        $(document).provinceField();
        $(document).variantPrices();
        $(document).variantImages();
    });
})(jQuery);
