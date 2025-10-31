<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.order.address_history', 'Sylius\Bundle\AdminBundle\Twig\Component\Order\AddressHistoryComponent')
        ->args([service('sylius.repository.address_log_entry')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:order:address_history']);

    $services->set('sylius_admin.twig.component.order.form', 'Sylius\Bundle\AdminBundle\Twig\Component\Order\FormComponent')
        ->args([
            service('sylius.repository.order'),
            service('form.factory'),
            '%sylius.model.order.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\OrderType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:order:form']);
};
