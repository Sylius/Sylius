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

use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeRepositoryPass implements CompilerPassInterface
{
    /**
     * Adds serialized themes to theme repository.
     *
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /** @var ConfigurationProviderInterface|CompilerPassInterface $configurationProvider */
        $configurationProvider = $container->get('sylius.theme.configuration.provider');

        /** @var ThemeFactoryInterface $themeFactory */
        $themeFactory = $container->get('sylius.theme.factory');

        $themeRepositoryDefinition = $container->findDefinition('sylius.theme.repository');

        foreach ($configurationProvider->provideAll() as $themeConfiguration) {
            $theme = $themeFactory->createFromArray($themeConfiguration);

            $themeRepositoryDefinition->addMethodCall('addSerialized', [serialize($theme)]);
        }

        if ($configurationProvider instanceof CompilerPassInterface) {
            $configurationProvider->process($container);
        }
    }
}
