<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\ShopBundle\Form\Type\AddressType;
use Sylius\Bundle\ShopBundle\Form\Type\AddToCartType;
use Sylius\Bundle\ShopBundle\Form\Type\CartItemType;
use Sylius\Bundle\ShopBundle\Form\Type\CartType;
use Sylius\Bundle\ShopBundle\Form\Type\Checkout\AddressType as CheckoutAddressType;
use Sylius\Bundle\ShopBundle\Form\Type\Checkout\SelectPaymentType;
use Sylius\Bundle\ShopBundle\Form\Type\Checkout\SelectShippingType;
use Sylius\Bundle\ShopBundle\Form\Type\CustomerProfileType;
use Sylius\Bundle\ShopBundle\Form\Type\CustomerRegistrationType;
use Sylius\Bundle\ShopBundle\Form\Type\Product\ProductReviewType;
use Sylius\Bundle\ShopBundle\Form\Type\UserChangePasswordType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.form.type.address', AddressType::class)
        ->args([service('sylius.repository.country')])
        ->tag('form.type')
    ;

    $services->set('sylius_shop.form.type.add_to_cart', AddToCartType::class)->tag('form.type');

    $services->set('sylius_shop.form.type.cart', CartType::class)->tag('form.type');

    $services->set('sylius_shop.form.type.cart_item', CartItemType::class)->tag('form.type');

    $services
        ->set('sylius_shop.form.type.customer_registration', CustomerRegistrationType::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_registration.validation_groups%',
            service('sylius.repository.customer'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius_shop.form.type.customer_profile', CustomerProfileType::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_profile.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services->set('sylius_shop.form.type.user_change_password', UserChangePasswordType::class)->tag('form.type');

    $services->set('sylius_shop.form.type.checkout.address', CheckoutAddressType::class)->tag('form.type');

    $services->set('sylius_shop.form.type.checkout.select_shipping', SelectShippingType::class)->tag('form.type');

    $services->set('sylius_shop.form.type.checkout.select_payment', SelectPaymentType::class)->tag('form.type');

    $services->set('sylius_shop.form.type.product_review', ProductReviewType::class)->tag('form.type');
};
