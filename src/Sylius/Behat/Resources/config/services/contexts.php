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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->import('contexts/api.php');
    $container->import('contexts/cli.php');
    $container->import('contexts/domain.php');
    $container->import('contexts/hook.php');
    $container->import('contexts/hybrid.php');
    $container->import('contexts/setup.php');
    $container->import('contexts/transform.php');
    $container->import('contexts/ui.php');
};
