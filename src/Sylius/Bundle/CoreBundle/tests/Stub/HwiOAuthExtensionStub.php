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

namespace Tests\Sylius\Bundle\CoreBundle\Stub;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Stands in for HWIOAuthBundle's extension, which SyliusCoreExtension looks up to decide whether to register the OAuth
 * integration services. HWIOAuthBundle is only a dev dependency, so its extension cannot be relied upon in tests.
 */
final class HwiOAuthExtensionStub extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
    }

    public function getAlias(): string
    {
        return 'hwi_oauth';
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return null;
    }
}
