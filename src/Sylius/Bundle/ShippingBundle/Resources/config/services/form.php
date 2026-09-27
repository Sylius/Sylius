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

use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistry;
use Sylius\Bundle\ShippingBundle\Form\Type\CalculatorChoiceType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShipmentShipType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShipmentType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryChoiceType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodChoiceType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodRuleChoiceType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodRuleCollectionType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodRuleType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodTranslationType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.form.type.shipping_method.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.shipping_method_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.shipping_method_rule.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.shipping_category.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.shipment.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.shipment_ship.validation_groups', ['sylius']);

    $services->set('sylius.form_registry.shipping_calculator', FormTypeRegistry::class);

    $services
        ->set('sylius.form.type.shipping_method', ShippingMethodType::class)
        ->args([
            '%sylius.model.shipping_method.class%',
            '%sylius.form.type.shipping_method.validation_groups%',
            ShippingMethodTranslationType::class,
            service('sylius.registry.shipping_calculator'),
            service('sylius.form_registry.shipping_calculator'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping_method_translation', ShippingMethodTranslationType::class)
        ->args([
            '%sylius.model.shipping_method_translation.class%',
            '%sylius.form.type.shipping_method_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping_method_choice', ShippingMethodChoiceType::class)
        ->args([
            service('sylius.resolver.shipping_methods'),
            service('sylius.registry.shipping_calculator'),
            service('sylius.repository.shipping_method'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping_method_rule', ShippingMethodRuleType::class)
        ->args([
            '%sylius.model.shipping_method_rule.class%',
            '%sylius.form.type.shipping_method_rule.validation_groups%',
            service('sylius.form_registry.shipping_method_rule_checker'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping_method_rule_collection', ShippingMethodRuleCollectionType::class)
        ->args([service('sylius.registry.shipping_method_rule_checker')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping_method_rule_choice', ShippingMethodRuleChoiceType::class)
        ->args(['%sylius.shipping_method_rules%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping_category', ShippingCategoryType::class)
        ->args([
            '%sylius.model.shipping_category.class%',
            '%sylius.form.type.shipping_category.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping_category_choice', ShippingCategoryChoiceType::class)
        ->args([service('sylius.repository.shipping_category')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipment', ShipmentType::class)
        ->args([
            '%sylius.model.shipment.class%',
            '%sylius.form.type.shipment.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipment_ship', ShipmentShipType::class)
        ->args([
            '%sylius.model.shipment.class%',
            '%sylius.form.type.shipment_ship.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping_calculator_choice', CalculatorChoiceType::class)
        ->args(['%sylius.shipping_calculators%'])
        ->tag('form.type')
    ;
};
