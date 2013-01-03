<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all settings schemas in the schema registry.
 * Save the configuration names in parameter for the provider.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class RegisterSchemasPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius_settings.registry')) {
            return;
        }

        $schemaRegistry = $container->getDefinition('sylius_settings.registry');
        $namespaces = array();

        foreach ($container->findTaggedServiceIds('sylius_settings.schema') as $id => $attributes) {
            $namespace = $attributes[0]['namespace'];
            $namespaces[] = $namespace;

            $schemaRegistry->addMethodCall('registerSchema', array($namespace, new Reference($id)));
        }

        $container->setParameter('sylius_settings.namespaces', $namespaces);
    }
}
