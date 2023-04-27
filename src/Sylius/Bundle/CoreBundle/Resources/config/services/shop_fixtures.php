<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $container->import('shop_fixtures/default_values.php');
    $container->import('shop_fixtures/factories.php');
    $container->import('shop_fixtures/fixtures.php');
    $container->import('shop_fixtures/stories.php');
    $container->import('shop_fixtures/transformers.php');
    $container->import('shop_fixtures/updaters.php');
};
