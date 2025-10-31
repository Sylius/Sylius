<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.product_variant.form', 'Sylius\Bundle\AdminBundle\Twig\Component\ProductVariant\FormComponent')
        ->args([
            service('sylius.repository.product_variant'),
            service('form.factory'),
            '%sylius.model.product_variant.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ProductVariantType',
            service('sylius.factory.product_variant'),
            service('sylius.repository.product'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_variant:form']);
};
