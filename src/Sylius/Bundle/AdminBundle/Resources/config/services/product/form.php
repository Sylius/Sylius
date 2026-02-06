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

use Sylius\Bundle\AdminBundle\Form\DataTransformer\ProductsToProductAssociationsTransformer;
use Sylius\Bundle\AdminBundle\Form\Type\ProductAssociationsType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.form.type.product', ProductType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_associations', ProductAssociationsType::class)
        ->args([service('sylius_admin.form.type.data_transformer.products_to_product_associations')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.data_transformer.products_to_product_associations', ProductsToProductAssociationsTransformer::class)
        ->args([
            service('sylius.factory.product_association'),
            service('sylius.repository.product_association_type'),
        ]);
};
