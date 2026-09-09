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

use Sylius\Behat\Element\Shop\Account\RegisterElement;
use Sylius\Behat\Element\Shop\CartWidgetElement;
use Sylius\Behat\Element\Shop\CartWidgetElementInterface;
use Sylius\Behat\Element\Shop\CheckoutSubtotalElement;
use Sylius\Behat\Element\Shop\CheckoutSubtotalElementInterface;
use Sylius\Behat\Element\Shop\MenuElement;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.behat.element.shop.account.register', RegisterElement::class)
        ->parent('sylius.behat.element')
        ->args([service('sylius.behat.shared_storage')])
    ;

    $services
        ->set('sylius.behat.element.shop.menu', MenuElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set(CartWidgetElementInterface::class, CartWidgetElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set(CheckoutSubtotalElementInterface::class, CheckoutSubtotalElement::class)
        ->parent('sylius.behat.element')
    ;
};
