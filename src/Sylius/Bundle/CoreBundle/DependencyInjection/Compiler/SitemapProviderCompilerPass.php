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
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sylius.sitemap.service.builder')) {
            return;
        }

        $builderDefinition = $container->findDefinition('sylius.sitemap.service.builder');
        $taggedProviders = $container->findTaggedServiceIds('sylius.sitemap.provider');

        foreach ($taggedProviders as $id => $provider) {
            $builderDefinition->addMethodCall(
                'addProvider',
                array(new Reference($id))
            );
        }
    }
}
