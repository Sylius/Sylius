<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ResourceConfigurationBuilderInterface
{
    /**
     * @param ArrayNodeDefinition $bundleRootDefinition
     *
     * @return ArrayNodeDefinition Resources node definition
     */
    public function initResourcesConfiguration(ArrayNodeDefinition $bundleRootDefinition);

    /**
     * @param ArrayNodeDefinition $resourcesDefinition Resources node builder of the bundle
     * @param SyliusResource $syliusResource
     *
     * @return ArrayNodeDefinition Resource node definition
     */
    public function addSyliusResource(ArrayNodeDefinition $resourcesDefinition, SyliusResource $syliusResource);
}
