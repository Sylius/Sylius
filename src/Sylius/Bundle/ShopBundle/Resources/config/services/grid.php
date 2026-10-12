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

use Sylius\Bundle\ShopBundle\Form\Type\Grid\Filter\TaxonFilterType;
use Sylius\Bundle\ShopBundle\Grid\Filter\ProductTaxonFilter;
use Sylius\Bundle\ShopBundle\Grid\Mutator\LiveProductGridMutator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_shop.grid.account.order', \Sylius\Bundle\ShopBundle\Grid\Account\OrderGrid::class)
        ->tag('sylius.invokable_grid')
    ;

    $services->alias(\Sylius\Bundle\ShopBundle\Grid\Account\OrderGridInterface::class, 'sylius_shop.grid.account.order');

    $services->set('sylius_shop.grid.product', \Sylius\Bundle\ShopBundle\Grid\ProductGrid::class)
        ->tag('sylius.invokable_grid')
    ;

    $services->alias(\Sylius\Bundle\ShopBundle\Grid\ProductGridInterface::class, 'sylius_shop.grid.product');

    // Experimental: only tagged as a grid mutator when "sylius_shop.product_grid.live" is enabled
    $services->set('sylius_shop.grid_mutator.live_product', LiveProductGridMutator::class);

    $services
        ->set('sylius_shop.grid_filter.product_taxon', ProductTaxonFilter::class)
        ->args([
            '%sylius.model.product_taxon.class%',
            '%sylius.model.taxon.class%',
        ])
        ->tag('sylius.grid_filter', ['type' => ProductTaxonFilter::TYPE, 'form_type' => TaxonFilterType::class])
    ;

    $services
        ->set('sylius_shop.form.type.grid.filter.taxon', TaxonFilterType::class)
        ->args([
            service('sylius.repository.taxon'),
            service('sylius.context.channel'),
            service('sylius.context.locale'),
        ])
        ->tag('form.type')
    ;
};
