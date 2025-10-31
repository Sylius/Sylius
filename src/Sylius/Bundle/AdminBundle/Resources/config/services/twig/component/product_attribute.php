<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.product_attribute.form', 'Sylius\Bundle\AdminBundle\Twig\Component\ProductAttribute\FormComponent')
        ->args([
            service('sylius.repository.product_attribute'),
            service('form.factory'),
            '%sylius.model.product_attribute.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ProductAttributeType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_attribute:form']);
};
