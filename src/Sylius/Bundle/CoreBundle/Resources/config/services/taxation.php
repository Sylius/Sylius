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

use Sylius\Bundle\CoreBundle\Taxation\Strategy\TaxCalculationStrategy;
use Sylius\Component\Core\Taxation\Applicator\OrderItemsTaxesApplicator;
use Sylius\Component\Core\Taxation\Applicator\OrderItemUnitsTaxesApplicator;
use Sylius\Component\Core\Taxation\Applicator\OrderShipmentTaxesApplicator;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistry;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.tax_calculation_strategy.interface', TaxCalculationStrategyInterface::class);

    $services
        ->set('sylius.registry.tax_calculation_strategy', PrioritizedServiceRegistry::class)
        ->args([
            '%sylius.tax_calculation_strategy.interface%',
            'Tax calculation strategy',
        ])
    ;

    $services
        ->set('sylius.applicator.taxation.order_shipment', OrderShipmentTaxesApplicator::class)
        ->args([
            service('sylius.tax_calculator'),
            service('sylius.factory.adjustment'),
            service('sylius.resolver.tax_rate'),
        ])
        ->tag('sylius.taxation.items.applicator')
        ->tag('sylius.taxation.item_units.applicator')
    ;

    $services
        ->set('sylius.applicator.taxation.order_items', OrderItemsTaxesApplicator::class)
        ->args([
            service('sylius.tax_calculator'),
            service('sylius.factory.adjustment'),
            service('sylius.distributor.integer'),
            service('sylius.resolver.tax_rate'),
            service('sylius.distributor.proportional_integer'),
        ])
        ->tag('sylius.taxation.items.applicator')
    ;

    $services
        ->set('sylius.applicator.taxation.order_item_units', OrderItemUnitsTaxesApplicator::class)
        ->args([
            service('sylius.tax_calculator'),
            service('sylius.factory.adjustment'),
            service('sylius.resolver.tax_rate'),
            service('sylius.distributor.proportional_integer'),
        ])
        ->tag('sylius.taxation.item_units.applicator')
    ;

    $services
        ->set('sylius.strategy.taxation.tax_calculation.order_items_based', TaxCalculationStrategy::class)
        ->args([
            'order_items_based',
            tagged_iterator('sylius.taxation.items.applicator'),
        ])
        ->tag('sylius.taxation.calculation_strategy', ['type' => 'order_items_based', 'label' => 'Order items based'])
    ;

    $services
        ->set('sylius.strategy.taxation.tax_calculation.order_item_units_based', TaxCalculationStrategy::class)
        ->args([
            'order_item_units_based',
            tagged_iterator('sylius.taxation.item_units.applicator'),
        ])
        ->tag('sylius.taxation.calculation_strategy', ['type' => 'order_item_units_based', 'label' => 'Order item units based'])
    ;
};
