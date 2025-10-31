<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.product_option.form', 'Sylius\Bundle\AdminBundle\Twig\Component\ProductOption\FormComponent')
        ->args([
            service('sylius.repository.product_option'),
            service('form.factory'),
            '%sylius.model.product_option.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ProductOptionType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_option:form']);
};
