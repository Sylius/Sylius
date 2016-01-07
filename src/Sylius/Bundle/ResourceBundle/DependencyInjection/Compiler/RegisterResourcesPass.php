<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds all resources to the registry.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RegisterResourcesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('sylius.resources')) {
            return;
        }

        $resources = $container->getParameter('sylius.resources');
        $registry = $container->getDefinition('sylius.resource_registry');

        foreach ($resources as $alias => $configuration) {
            $registry->addMethodCall('addFromAliasAndConfiguration', array($alias, $configuration));
        }
    }
}
