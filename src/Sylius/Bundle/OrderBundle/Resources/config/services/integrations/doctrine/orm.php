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

use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderItemRepository;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $parameters->set('sylius.repository.order.class', OrderRepository::class);
    $parameters->set('sylius.repository.order_item.class', OrderItemRepository::class);
};
