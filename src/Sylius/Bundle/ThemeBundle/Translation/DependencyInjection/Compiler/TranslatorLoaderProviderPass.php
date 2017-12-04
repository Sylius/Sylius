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

namespace Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TranslatorLoaderProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        try {
            $loaderProvider = $container->findDefinition('sylius.theme.translation.loader_provider');
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds('translation.loader');
        $loaders = [];
        foreach ($taggedServices as $id => $attributes) {
            $loader = $container->findDefinition($id);
            $loader->setLazy(true);

            $loaders[$attributes[0]['alias']] = new Reference($id);

            if (isset($attributes[0]['legacy-alias'])) {
                $loaders[$attributes[0]['legacy-alias']] = new Reference($id);
            }
        }

        $loaderProvider->replaceArgument(0, $loaders);
    }
}
