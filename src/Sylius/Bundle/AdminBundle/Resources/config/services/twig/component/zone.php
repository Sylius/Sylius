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

use Sylius\Bundle\AdminBundle\Form\Type\ZoneType;
use Sylius\Bundle\AdminBundle\Twig\Component\Zone\FormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.twig.component.zone.form', FormComponent::class)
        ->args([
            service('sylius.repository.zone'),
            service('form.factory'),
            '%sylius.model.zone.class%',
            ZoneType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:zone:form'])
    ;
};
