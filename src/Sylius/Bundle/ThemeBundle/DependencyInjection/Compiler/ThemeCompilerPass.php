<?php

namespace Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ThemeBundle\Exception\InvalidArgumentException;
use Sylius\Bundle\ThemeBundle\Model\Theme;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('sylius.repository.theme');

        $loader = $container->get('sylius.loader.theme');

        $serializedThemes = [];
        $themeFiles = $container->get('sylius.locator.theme')->locate('theme.json', null, false);
        foreach ($themeFiles as $themeFile) {
            $serializedThemes[] = serialize($loader->load($themeFile));
            $container->addResource(new FileResource($themeFile));
        }

        $definition->addArgument($serializedThemes);
    }
}