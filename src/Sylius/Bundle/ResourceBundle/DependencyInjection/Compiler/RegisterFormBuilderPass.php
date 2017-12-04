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

final class RegisterFormBuilderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.form_builder')) {
            return;
        }

        $registry = $container->findDefinition('sylius.registry.form_builder');

        foreach ($container->findTaggedServiceIds('sylius.default_resource_form.builder') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged grid drivers needs to have `type` attribute.');
            }

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }
    }
}
