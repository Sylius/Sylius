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

use Sylius\Bundle\PromotionBundle\Console\Command\GenerateCouponsCommand;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.console.command.generate_coupons', GenerateCouponsCommand::class)
        ->args([
            service('sylius.repository.promotion'),
            service('sylius.generator.promotion_coupon'),
        ])
        ->tag('console.command')
    ;
};
