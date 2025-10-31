<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.zone.form', 'Sylius\Bundle\AdminBundle\Twig\Component\Zone\FormComponent')
        ->args([
            service('sylius.repository.zone'),
            service('form.factory'),
            '%sylius.model.zone.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ZoneType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:zone:form']);
};
