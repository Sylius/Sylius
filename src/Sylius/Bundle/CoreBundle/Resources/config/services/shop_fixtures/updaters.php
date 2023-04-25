<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\ShopFixtures\Transformer\CustomerTransformer;
use Sylius\Bundle\CoreBundle\ShopFixtures\Transformer\CustomerTransformerInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Updater\CustomerUpdater;
use Sylius\Bundle\CoreBundle\ShopFixtures\Updater\CustomerUpdaterInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Updater\ShopUserUpdater;
use Sylius\Bundle\CoreBundle\ShopFixtures\Updater\ShopUserUpdaterInterface;

return static function (ContainerConfigurator $container) {
    $container->services()

        ->set('sylius.shop_fixtures.updater.customer', CustomerUpdater::class)
        ->alias(CustomerUpdaterInterface::class, 'sylius.shop_fixtures.updater.customer')

        ->set('sylius.shop_fixtures.updater.shop_user', ShopUserUpdater::class)
            ->args([
                service('sylius.shop_fixtures.updater.customer'),
            ])
        ->alias(ShopUserUpdaterInterface::class, 'sylius.shop_fixtures.updater.shop_user')

    ;
};
