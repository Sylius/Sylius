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

use Sylius\Bundle\TaxationBundle\Form\Type\TaxCalculatorChoiceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxRateType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.form.type.tax_category.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.tax_rate.validation_groups', ['sylius']);

    $services
        ->set('sylius.form.type.tax_category', TaxCategoryType::class)
        ->args([
            '%sylius.model.tax_category.class%',
            '%sylius.form.type.tax_category.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.tax_category_choice', TaxCategoryChoiceType::class)
        ->args([service('sylius.repository.tax_category')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.tax_rate', TaxRateType::class)
        ->args([
            '%sylius.model.tax_rate.class%',
            '%sylius.form.type.tax_rate.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.tax_calculator_choice', TaxCalculatorChoiceType::class)
        ->args(['%sylius.tax_calculators%'])
        ->tag('form.type')
    ;
};
