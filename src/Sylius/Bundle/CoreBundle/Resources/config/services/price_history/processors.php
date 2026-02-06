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

use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessor;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.processor.price_history.product_lowest_price_before_discount', ProductLowestPriceBeforeDiscountProcessor::class)
        ->args([
            service('sylius.repository.channel_pricing_log_entry'),
            service('sylius.repository.channel'),
        ]);

    $services->alias(ProductLowestPriceBeforeDiscountProcessorInterface::class, 'sylius.processor.price_history.product_lowest_price_before_discount');
};
