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

use Sylius\Bundle\PromotionBundle\Criteria\DateRange;
use Sylius\Bundle\PromotionBundle\Criteria\Enabled;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.catalog_promotion.criteria.enabled', Enabled::class)
        ->tag('sylius.catalog_promotion.criteria')
    ;

    $services
        ->set('sylius.catalog_promotion.criteria.date_range', DateRange::class)
        ->args([service('clock')])
        ->tag('sylius.catalog_promotion.criteria')
    ;
};
