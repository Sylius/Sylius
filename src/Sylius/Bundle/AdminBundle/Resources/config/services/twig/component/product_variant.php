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

use Sylius\Bundle\AdminBundle\Form\Type\ProductVariantType;
use Sylius\Bundle\AdminBundle\Twig\Component\ProductVariant\FormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.twig.component.product_variant.form', FormComponent::class)
        ->args([
            service('sylius.repository.product_variant'),
            service('form.factory'),
            '%sylius.model.product_variant.class%',
            ProductVariantType::class,
            service('sylius.factory.product_variant'),
            service('sylius.repository.product'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_variant:form'])
    ;
};
