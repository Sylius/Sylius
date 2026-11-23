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

use Sylius\Bundle\CoreBundle\Installer\Requirement\ExtensionsRequirements;
use Sylius\Bundle\CoreBundle\Installer\Requirement\FilesystemRequirements;
use Sylius\Bundle\CoreBundle\Installer\Requirement\SettingsRequirements;
use Sylius\Bundle\CoreBundle\Installer\Requirement\SyliusRequirements;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.requirements.installer.sylius', SyliusRequirements::class)
        ->args([[
            inline_service(SettingsRequirements::class)
                ->args([service('translator')]),
            inline_service(ExtensionsRequirements::class)
                ->args([service('translator')]),
            inline_service(FilesystemRequirements::class)
                ->args([
                    service('translator'),
                    '%kernel.cache_dir%',
                    '%kernel.logs_dir%',
                ]),
        ]])
    ;
};
