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

return static function (ContainerConfigurator $container): void {
    // doctrine/doctrine-bundle 2.x enables native lazy objects by default, but they require PHP 8.4+.
    // On PHP < 8.4 (where only doctrine-bundle 2.x is installable) they must be disabled so the
    // container can compile. On PHP 8.4+ the default is kept - and doctrine-bundle 3.x no longer
    // allows the option to be set at all.
    if (\PHP_VERSION_ID >= 80400) {
        return;
    }

    $container->extension('doctrine', [
        'orm' => [
            'enable_native_lazy_objects' => false,
        ],
    ]);
};
