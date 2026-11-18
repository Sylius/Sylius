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

use Sylius\Behat\Page\Shop\Order\ShowPage;
use Sylius\Behat\Page\Shop\Order\ThankYouPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.shop.order.thank_you.class', ThankYouPage::class);
    $parameters->set('sylius.behat.page.shop.order.show.class', ShowPage::class);

    $services
        ->set('sylius.behat.page.shop.order.thank_you', '%sylius.behat.page.shop.order.thank_you.class%')
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.order.show', '%sylius.behat.page.shop.order.show.class%')
        ->parent('sylius.behat.symfony_page')
    ;
};
