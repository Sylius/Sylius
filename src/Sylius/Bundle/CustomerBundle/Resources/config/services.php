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

use Sylius\Bundle\CustomerBundle\Form\Type\CustomerChoiceType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerGroupChoiceType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerGroupCodeChoiceType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerGroupType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.form.type.customer.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.customer_profile.validation_groups', ['sylius', 'sylius_customer_profile']);
    $parameters->set('sylius.form.type.customer_group.validation_groups', ['sylius']);

    $services->set('sylius.form.type.customer', CustomerType::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.customer_profile', CustomerProfileType::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_profile.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.customer_choice', CustomerChoiceType::class)
        ->args([service('sylius.repository.customer')])
        ->tag('form.type');

    $services->set('sylius.form.type.customer_group', CustomerGroupType::class)
        ->args([
            '%sylius.model.customer_group.class%',
            '%sylius.form.type.customer_group.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.customer_group_choice', CustomerGroupChoiceType::class)
        ->args([service('sylius.repository.customer_group')])
        ->tag('form.type');

    $services->set('sylius.form.type.customer_group_code_choice', CustomerGroupCodeChoiceType::class)
        ->args([service('sylius.repository.customer_group')])
        ->tag('form.type');
};
