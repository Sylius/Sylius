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

use Sylius\Behat\Page\Shop\PaymentRequest\PaymentMethodNotifyPage;
use Sylius\Behat\Page\Shop\PaymentRequest\PaymentRequestNotifyPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.shop.payment_request.payment_method_notify.class', PaymentMethodNotifyPage::class);
    $parameters->set('sylius.behat.page.shop.payment_request.payment_request_notify.class', PaymentRequestNotifyPage::class);

    $services
        ->set('sylius.behat.page.shop.payment_request.payment_method_notify', '%sylius.behat.page.shop.payment_request.payment_method_notify.class%')
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.payment_request.payment_request_notify', '%sylius.behat.page.shop.payment_request.payment_request_notify.class%')
        ->parent('sylius.behat.symfony_page')
    ;
};
