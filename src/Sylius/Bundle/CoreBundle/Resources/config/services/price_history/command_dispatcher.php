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

use Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings', BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher::class)
        ->args([
            service('sylius.repository.channel_pricing'),
            service('sylius.command_bus'),
            '%sylius_core.price_history.batch_size%',
        ]);

    $services->alias(ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface::class, 'sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings');
};
