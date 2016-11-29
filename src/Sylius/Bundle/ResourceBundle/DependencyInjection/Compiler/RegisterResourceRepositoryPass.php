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
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class RegisterResourceRepositoryPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('sylius.resources')) {
            return;
        }

        $resources = $container->getParameter('sylius.resources');

        if ($container->has('sylius.registry.resource_repository')) {
            $repositoryRegistry = $container->findDefinition('sylius.registry.resource_repository');

            foreach ($resources as $alias => $configuration) {
                $repositoryId = sprintf('sylius.repository.%s', str_replace('sylius.', '', $alias));

                if ($container->has($repositoryId)) {
                    $repositoryRegistry->addMethodCall('register', [$alias, new Reference($repositoryId)]);
                }
            }
        }
    }
}
