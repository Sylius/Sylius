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

use Sylius\Bundle\AdminBundle\Form\Type\OrderType;
use Sylius\Bundle\AdminBundle\Twig\Component\Order\AddressHistoryComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Order\FormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.order.address_history', AddressHistoryComponent::class)
        ->args([service('sylius.repository.address_log_entry')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:order:address_history']);

    $services->set('sylius_admin.twig.component.order.form', FormComponent::class)
        ->args([
            service('sylius.repository.order'),
            service('form.factory'),
            '%sylius.model.order.class%',
            OrderType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:order:form']);
};
