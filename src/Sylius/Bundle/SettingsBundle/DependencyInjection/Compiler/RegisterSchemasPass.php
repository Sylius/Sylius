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
        if (!$container->hasDefinition('sylius.settings.schema_registry')) {
            return;
        }

        $schemaRegistry = $container->getDefinition('sylius.settings.schema_registry');

        foreach ($container->findTaggedServiceIds('sylius.settings_schema') as $id => $attributes) {
            $schemaRegistry->addMethodCall('registerSchema', array(new Reference($id), isset($attributes[0]['namespace']) ? $attributes[0]['namespace'] : null));
        }
    }
}
