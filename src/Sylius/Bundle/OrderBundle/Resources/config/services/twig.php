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

use Sylius\Bundle\OrderBundle\Twig\AggregateAdjustmentsExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.twig.extension.aggregate_adjustments', AggregateAdjustmentsExtension::class)
        ->private()
        ->args([service('sylius.aggregator.adjustments_by_label')])
        ->tag('twig.extension');
};
