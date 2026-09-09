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

use Sylius\Behat\Page\Admin\Product\CreateConfigurableProductPage;
use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPage;
use Sylius\Behat\Page\Admin\Product\IndexPage;
use Sylius\Behat\Page\Admin\Product\IndexPerTaxonPage;
use Sylius\Behat\Page\Admin\Product\ShowPage;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPage;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPage;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.behat.page.admin.product.create_configurable.class', CreateConfigurableProductPage::class);
    $parameters->set('sylius.behat.page.admin.product.create_simple.class', CreateSimpleProductPage::class);
    $parameters->set('sylius.behat.page.admin.product.index.class', IndexPage::class);
    $parameters->set('sylius.behat.page.admin.product.index_per_taxon.class', IndexPerTaxonPage::class);
    $parameters->set('sylius.behat.page.admin.product.show.class', ShowPage::class);
    $parameters->set('sylius.behat.page.admin.product.update_simple.class', UpdateSimpleProductPage::class);
    $parameters->set('sylius.behat.page.admin.product.update_configurable.class', UpdateConfigurableProductPage::class);

    $services
        ->set('sylius.behat.page.admin.product.create_configurable', '%sylius.behat.page.admin.product.create_configurable.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args([
            'sylius_admin_product_create',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.product.create_simple', '%sylius.behat.page.admin.product.create_simple.class%')
        ->parent('sylius.behat.page.admin.crud.create')
        ->args([
            'sylius_admin_product_create',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.product.index', '%sylius.behat.page.admin.product.index.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args([
            'sylius_admin_product_index',
            service('sylius.behat.checker.image_existence'),
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.product.index_per_taxon', '%sylius.behat.page.admin.product.index_per_taxon.class%')
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_admin_product_taxon_index'])
    ;

    $services
        ->set('sylius.behat.page.admin.product.update_configurable', '%sylius.behat.page.admin.product.update_configurable.class%')
        ->parent('sylius.behat.page.admin.crud.update')
        ->args([
            'sylius_admin_product_update',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.product.update_simple', '%sylius.behat.page.admin.product.update_simple.class%')
        ->parent('sylius.behat.page.admin.crud.update')
        ->args([
            'sylius_admin_product_update',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.page.admin.product.show_page', '%sylius.behat.page.admin.product.show.class%')
        ->parent('sylius.behat.symfony_page')
    ;
};
