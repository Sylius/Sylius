<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Compiler pass that registers all processor operations.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class RegisterOperationsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius_sales.processor')) {
            return;
        }

        $processor = $container->getDefinition('sylius_sales.processor');
        foreach ($container->findTaggedServiceIds('sylius_sales.operation') as $id => $attributes) {
            $processor->addMethodCall('registerOperation', array(new Reference($id)));
        }
    }
}
