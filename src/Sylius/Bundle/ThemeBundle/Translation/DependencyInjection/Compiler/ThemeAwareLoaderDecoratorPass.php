<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use Sylius\Bundle\ThemeBundle\Translation\Loader\ThemeAwareLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeAwareLoaderDecoratorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->decorateTranslationLoaders($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function decorateTranslationLoaders(ContainerBuilder $container)
    {
        $loaders = $container->findTaggedServiceIds('translation.loader');
        foreach (array_keys($loaders) as $id) {
            $container
                ->register('sylius.theme.translation.loader.' . $id, ThemeAwareLoader::class)
                ->setDecoratedService($id)
                ->setArguments([
                    new Reference('sylius.theme.translation.loader.' . $id . '.inner'),
                    new Reference('sylius.theme.repository'),
                ])
            ;
        }
    }
}
