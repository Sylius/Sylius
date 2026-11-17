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

use Sylius\Behat\Page\Admin\CatalogPromotion\CreatePage;
use Sylius\Behat\Page\Admin\CatalogPromotion\ProductVariant\IndexPage;
use Sylius\Behat\Page\Admin\CatalogPromotion\ShowPage;
use Sylius\Behat\Page\Admin\CatalogPromotion\UpdatePage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.catalog_promotion.create.class', CreatePage::class);
    $parameters->set('sylius.behat.page.admin.catalog_promotion.index.class', '%sylius.behat.page.admin.crud.index.class%');
    $parameters->set('sylius.behat.page.admin.catalog_promotion.show.class', ShowPage::class);
    $parameters->set('sylius.behat.page.admin.catalog_promotion.product_variant.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.catalog_promotion.update.class', UpdatePage::class);

    $services
        ->set('sylius.behat.page.admin.catalog_promotion.create', '%sylius.behat.page.admin.catalog_promotion.create.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_catalog_promotion_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.catalog_promotion.index', '%sylius.behat.page.admin.catalog_promotion.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_catalog_promotion_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.catalog_promotion.product_variant.index', '%sylius.behat.page.admin.catalog_promotion.product_variant.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_catalog_promotion_product_variant_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.catalog_promotion.update', '%sylius.behat.page.admin.catalog_promotion.update.class%')
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_catalog_promotion_update'])
    ;

    $services
        ->set('sylius.behat.page.admin.catalog_promotion.show', '%sylius.behat.page.admin.catalog_promotion.show.class%')
        ->parent('sylius.behat.symfony_page')
    ;
};
