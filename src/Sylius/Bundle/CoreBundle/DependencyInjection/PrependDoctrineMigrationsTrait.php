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

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependDoctrineMigrationsTrait
{
    private function prependDoctrineMigrations(ContainerBuilder $container): void
    {
        if (
            !$container->hasExtension('doctrine_migrations') ||
            !$container->hasExtension('sylius_labs_doctrine_migrations_extra')
        ) {
            return;
        }

        if (
            $container->hasParameter('sylius_core.prepend_doctrine_migrations') &&
            !$container->getParameter('sylius_core.prepend_doctrine_migrations')
        ) {
            return;
        }

        $doctrineConfig = $container->getExtensionConfig('doctrine_migrations');
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => \array_merge(\array_pop($doctrineConfig)['migrations_paths'] ?? [], [
                $this->getMigrationsNamespace() => $this->getMigrationsDirectory(),
            ]),
        ]);

        $container->prependExtensionConfig('sylius_labs_doctrine_migrations_extra', [
            'migrations' => [
                $this->getMigrationsNamespace() => $this->getNamespacesOfMigrationsExecutedBefore(),
            ],
        ]);
    }

    abstract protected function getMigrationsNamespace(): string;

    abstract protected function getMigrationsDirectory(): string;

    abstract protected function getNamespacesOfMigrationsExecutedBefore(): array;
}
