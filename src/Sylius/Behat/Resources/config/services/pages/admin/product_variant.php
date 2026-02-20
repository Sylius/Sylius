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

use Sylius\Behat\Page\Admin\ProductVariant\CreatePage;
use Sylius\Behat\Page\Admin\ProductVariant\GeneratePage;
use Sylius\Behat\Page\Admin\ProductVariant\IndexPage;
use Sylius\Behat\Page\Admin\ProductVariant\UpdatePage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.product_variant.create.class', CreatePage::class);
    $parameters->set('sylius.behat.page.admin.product_variant.generate.class', GeneratePage::class);
    $parameters->set('sylius.behat.page.admin.product_variant.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.product_variant.update.class', UpdatePage::class);

    $services->defaults()->public();

    $services
        ->set('sylius.behat.page.admin.product_variant.create', '%sylius.behat.page.admin.product_variant.create.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['sylius_admin_product_variant_create'])
    ;

    $services
        ->set('sylius.behat.page.admin.product_variant.generate', '%sylius.behat.page.admin.product_variant.generate.class%')
        ->private()
        ->parent('sylius.behat.symfony_page')
        ->args(['product_variant'])
    ;

    $services
        ->set('sylius.behat.page.admin.product_variant.index', '%sylius.behat.page.admin.product_variant.index.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_product_variant_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.product_variant.update', '%sylius.behat.page.admin.product_variant.update.class%')
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_product_variant_update'])
    ;
};
