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

use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculator;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.calculator.delay_stamp', DelayStampCalculator::class);

    $services->alias(DelayStampCalculatorInterface::class, 'sylius.calculator.delay_stamp');
};
