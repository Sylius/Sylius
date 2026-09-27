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

return static function (ContainerConfigurator $container) {
    $container->import('price_history/checkers.php');
    $container->import('price_history/command_dispatcher.php');
    $container->import('price_history/command_handler.php');
    $container->import('price_history/listeners.php');
    $container->import('price_history/logger.php');
    $container->import('price_history/processors.php');

    $services = $container->services();
};
