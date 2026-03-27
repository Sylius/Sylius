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

use Sylius\Behat\Page\Shop\Checkout\AddressPage;
use Sylius\Behat\Page\Shop\Checkout\CompletePage;
use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPage;
use Sylius\Behat\Page\Shop\Checkout\SelectShippingPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.shop.checkout.address.class', AddressPage::class);
    $parameters->set('sylius.behat.page.shop.checkout.select_payment.class', SelectPaymentPage::class);
    $parameters->set('sylius.behat.page.shop.checkout.select_shipping.class', SelectShippingPage::class);
    $parameters->set('sylius.behat.page.shop.checkout.complete.class', CompletePage::class);

    $services
        ->set('sylius.behat.page.shop.checkout.address', '%sylius.behat.page.shop.checkout.address.class%')
        ->parent('sylius.behat.page.shop.page')
        ->args([
            service('sylius.factory.address'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.page.shop.checkout.select_payment', '%sylius.behat.page.shop.checkout.select_payment.class%')
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.checkout.select_shipping', '%sylius.behat.page.shop.checkout.select_shipping.class%')
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.checkout.complete', '%sylius.behat.page.shop.checkout.complete.class%')
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')])
    ;
};
