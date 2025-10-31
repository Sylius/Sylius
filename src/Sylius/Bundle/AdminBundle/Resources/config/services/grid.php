<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.grid_filter.ux_autocomplete', 'Sylius\Component\Grid\Filter\EntityFilter')
        ->tag('sylius.grid_filter', ['type' => 'ux_autocomplete', 'form_type' => 'Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxAutocompleteFilterType']);

    $services->set('sylius_admin.grid_filter.ux_translatable_autocomplete', 'Sylius\Component\Grid\Filter\EntityFilter')
        ->tag('sylius.grid_filter', ['type' => 'ux_translatable_autocomplete', 'form_type' => 'Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxTranslatableAutocompleteFilterType']);
};
