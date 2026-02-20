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

use Sylius\Behat\Page\Shop\Product\ShowPage;
use Sylius\Behat\Page\Shop\Product\IndexPage;
use Sylius\Behat\Page\Shop\ProductReview\CreatePage as ReviewCreatePage;
use Sylius\Behat\Page\Shop\ProductReview\IndexPage as ReviewIndexPage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.shop.product.show.class', ShowPage::class);
    $parameters->set('sylius.behat.page.shop.product.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.shop.product_reviews.create.class', ReviewCreatePage::class);
    $parameters->set('sylius.behat.page.shop.product_reviews.index.class', ReviewIndexPage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.shop.product.show', '%sylius.behat.page.shop.product.show.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.page.shop.cart_summary')])
    ;

    $services
        ->set('sylius.behat.page.shop.product.index', '%sylius.behat.page.shop.product.index.class%')
        ->private()
        ->parent('sylius.behat.page.shop.page')
    ;

    $services
        ->set('sylius.behat.page.shop.product_reviews.create', '%sylius.behat.page.shop.product_reviews.create.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius.behat.page.shop.product_reviews.index', '%sylius.behat.page.shop.product_reviews.index.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
    ;
};
