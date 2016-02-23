<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeRepositoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /** @var ConfigurationProviderInterface $configurationProvider */
        $configurationProvider = $container->get('sylius.theme.configuration.provider');

        $themeRepositoryDefinition = $container->findDefinition('sylius.repository.theme');
        foreach ($configurationProvider->getConfigurations() as $themeConfiguration) {
            $themeDefinition = new Definition(null, [$themeConfiguration]);

            $themeDefinition->setFactory([
                new Reference('sylius.factory.theme'),
                'createFromArray',
            ]);

            $themeRepositoryDefinition->addMethodCall('add', [$themeDefinition]);
        }

        foreach ($configurationProvider->getResources() as $resource) {
            $container->addResource($resource);
        }
    }
}
