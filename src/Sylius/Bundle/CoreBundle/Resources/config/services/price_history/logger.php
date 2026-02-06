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

use Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLogger;
use Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.logger.price_history.price_change', PriceChangeLogger::class)
        ->args([
            service('sylius.factory.channel_pricing_log_entry'),
            service('sylius.manager.channel_pricing_log_entry'),
            service('clock'),
        ]);

    $services->alias(PriceChangeLoggerInterface::class, 'sylius.logger.price_history.price_change');
};
