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

// Calendar ^0.5 changed the location of behat services
return static function (ContainerConfigurator $containerConfigurator): void {
    $baseSyliusCalendarDir = __DIR__ . '/../../../../../../vendor/sylius/calendar/tests/Behat/Resources/services.yaml';
    if (file_exists($baseSyliusCalendarDir)) {
        $containerConfigurator->import($baseSyliusCalendarDir);

        return;
    }

    $appSyliusCalendarDir = __DIR__ . '/../../../../../../../../sylius/calendar/tests/Behat/Resources/services.yaml';
    if (file_exists($appSyliusCalendarDir)) {
        $containerConfigurator->import($appSyliusCalendarDir);
    }
};
