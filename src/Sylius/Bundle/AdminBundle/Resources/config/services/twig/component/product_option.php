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

use Sylius\Bundle\AdminBundle\Form\Type\ProductOptionType;
use Sylius\Bundle\AdminBundle\Twig\Component\ProductOption\FormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.twig.component.product_option.form', FormComponent::class)
        ->args([
            service('sylius.repository.product_option'),
            service('form.factory'),
            '%sylius.model.product_option.class%',
            ProductOptionType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_option:form'])
    ;
};
