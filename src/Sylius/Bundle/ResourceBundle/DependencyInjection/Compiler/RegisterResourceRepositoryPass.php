<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sylius.resources') || !$container->has('sylius.registry.resource_repository')) {
            return;
        }

        $resources = $container->getParameter('sylius.resources');

        $repositoryRegistry = $container->findDefinition('sylius.registry.resource_repository');

        foreach ($resources as $alias => $configuration) {
            list($applicationName, $resourceName) = explode('.', $alias, 2);
            $repositoryId = sprintf('%s.repository.%s', $applicationName, $resourceName);

            if ($container->has($repositoryId)) {
                $repositoryRegistry->addMethodCall('register', [$alias, new Reference($repositoryId)]);
            }
        }
    }
}
