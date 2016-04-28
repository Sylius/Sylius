<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ConfigurationSourceFactoryInterface
{
    /**
     * @param ArrayNodeDefinition $node
     */
    public function buildConfiguration(ArrayNodeDefinition $node);

    /**
     * @see ConfigurationProviderInterface
     *
     * @param ContainerBuilder $container
     * @param array $config
     *
     * @return Reference|Definition Configuration provider service
     */
    public function initializeSource(ContainerBuilder $container, array $config);

    /**
     * @return string
     */
    public function getName();
}
