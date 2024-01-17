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

namespace Sylius\Abstraction\StateMachine\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusStateMachineAbstractionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__, 2) . '/config/'));
        $loader->load('services.xml');

        $container->setParameter('sylius_abstraction.state_machine.default_adapter', $config['default_adapter']);
        $container->setParameter('sylius_abstraction.state_machine.graphs_to_adapters_mapping', $config['graphs_to_adapters_mapping']);
    }
}
