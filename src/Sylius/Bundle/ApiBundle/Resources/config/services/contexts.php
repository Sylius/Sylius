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

use Sylius\Bundle\ApiBundle\Context\TokenBasedUserContext;
use Sylius\Bundle\ApiBundle\Context\TokenValueBasedCartContext;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.context.user.token_based', TokenBasedUserContext::class)
        ->args([service('security.token_storage')]);

    $services->alias(UserContextInterface::class, 'sylius_api.context.user.token_based');

    $services->set('sylius_api.context.cart.token_value_based', TokenValueBasedCartContext::class)
        ->args([
            service('request_stack'),
            service('sylius.repository.order'),
            '%sylius.security.api_route%',
        ])
        ->tag('sylius.context.cart', ['priority' => -333]);
};
