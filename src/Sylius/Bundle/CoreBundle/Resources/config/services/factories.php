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

use Sylius\Bundle\CoreBundle\Factory\OrderFactory;
use Sylius\Bundle\CoreBundle\Factory\OrderFactoryInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.custom_factory.order', OrderFactory::class)
        ->decorate('sylius.factory.order')
        ->args([service('sylius.custom_factory.order.inner')]);

    $services->alias(OrderFactoryInterface::class, 'sylius.custom_factory.order');
};
