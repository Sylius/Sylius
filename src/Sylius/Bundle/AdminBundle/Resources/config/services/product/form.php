<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.form.type.product', 'Sylius\Bundle\AdminBundle\Form\Type\ProductType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_associations', 'Sylius\Bundle\AdminBundle\Form\Type\ProductAssociationsType')
        ->args([service('sylius_admin.form.type.data_transformer.products_to_product_associations')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.data_transformer.products_to_product_associations', 'Sylius\Bundle\AdminBundle\Form\DataTransformer\ProductsToProductAssociationsTransformer')
        ->args([
            service('sylius.factory.product_association'),
            service('sylius.repository.product_association_type'),
        ]);
};
