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

use Sylius\Bundle\CoreBundle\OAuth\UserProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.oauth.user_provider', UserProvider::class)
        ->lazy()
        ->args([
            '%sylius.model.shop_user.class%',
            service('sylius.factory.customer'),
            service('sylius.factory.shop_user'),
            service('sylius.repository.shop_user'),
            service('sylius.factory.oauth_user'),
            service('sylius.repository.oauth_user'),
            service('sylius.manager.shop_user'),
            service('sylius.canonicalizer'),
            service('sylius.repository.customer'),
        ]);
};
