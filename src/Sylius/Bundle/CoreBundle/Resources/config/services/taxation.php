<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.tax_calculation_strategy.interface', 'Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface');

    $services->set('sylius.registry.tax_calculation_strategy', 'Sylius\Component\Registry\PrioritizedServiceRegistry')
        ->args([
            '%sylius.tax_calculation_strategy.interface%',
            'Tax calculation strategy',
        ]);

    $services->set('sylius.applicator.taxation.order_shipment', 'Sylius\Component\Core\Taxation\Applicator\OrderShipmentTaxesApplicator')
        ->args([
            service('sylius.tax_calculator'),
            service('sylius.factory.adjustment'),
            service('sylius.resolver.tax_rate'),
        ])
        ->tag('sylius.taxation.items.applicator')
        ->tag('sylius.taxation.item_units.applicator');

    $services->set('sylius.applicator.taxation.order_items', 'Sylius\Component\Core\Taxation\Applicator\OrderItemsTaxesApplicator')
        ->args([
            service('sylius.tax_calculator'),
            service('sylius.factory.adjustment'),
            service('sylius.distributor.integer'),
            service('sylius.resolver.tax_rate'),
            service('sylius.distributor.proportional_integer'),
        ])
        ->tag('sylius.taxation.items.applicator');

    $services->set('sylius.applicator.taxation.order_item_units', 'Sylius\Component\Core\Taxation\Applicator\OrderItemUnitsTaxesApplicator')
        ->args([
            service('sylius.tax_calculator'),
            service('sylius.factory.adjustment'),
            service('sylius.resolver.tax_rate'),
            service('sylius.distributor.proportional_integer'),
        ])
        ->tag('sylius.taxation.item_units.applicator');

    $services->set('sylius.strategy.taxation.tax_calculation.order_items_based', 'Sylius\Bundle\CoreBundle\Taxation\Strategy\TaxCalculationStrategy')
        ->args([
            'order_items_based',
            tagged_iterator('sylius.taxation.items.applicator'),
        ])
        ->tag('sylius.taxation.calculation_strategy', ['type' => 'order_items_based', 'label' => 'Order items based']);

    $services->set('sylius.strategy.taxation.tax_calculation.order_item_units_based', 'Sylius\Bundle\CoreBundle\Taxation\Strategy\TaxCalculationStrategy')
        ->args([
            'order_item_units_based',
            tagged_iterator('sylius.taxation.item_units.applicator'),
        ])
        ->tag('sylius.taxation.calculation_strategy', ['type' => 'order_item_units_based', 'label' => 'Order item units based']);
};
