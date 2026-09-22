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

use Sylius\Bundle\ApiBundle\Security\OrderAdjustmentsVoter;
use Sylius\Bundle\ApiBundle\Security\ShopUserVoter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->tag('security.voter');

    $services->set('sylius_api.security.voter.shop_user', ShopUserVoter::class);

    $services
        ->set('sylius_api.security.voter.order_adjustments', OrderAdjustmentsVoter::class)
        ->args([service('sylius_api.provider.adjustment_order')])
        ->tag('security.voter')
    ;
};
