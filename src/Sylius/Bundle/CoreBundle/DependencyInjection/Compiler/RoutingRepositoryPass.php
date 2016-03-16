<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class RoutingRepositoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('sylius.repository_by_classes') &&
            $container->hasDefinition('sylius.route_provider')) {
            $repositoryByClasses = $container->getParameter('sylius.repository_by_classes');
            $routeProvider = $container->getDefinition('sylius.route_provider');

            foreach ($repositoryByClasses as $class => $repository) {
                $routeProvider->addMethodCall('addRepository', [$class, $repository]);
            }
        }
    }
}
