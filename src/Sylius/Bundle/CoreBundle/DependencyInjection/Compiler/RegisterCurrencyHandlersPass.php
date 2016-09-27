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
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class RegisterCurrencyHandlersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.handler.currency_change')) {
            return;
        }

        $compositeLocaleHandler = $container->findDefinition('sylius.handler.currency_change');
        foreach ($container->findTaggedServiceIds('sylius.currency.change_handler') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;

            $compositeLocaleHandler->addMethodCall('addHandler', [new Reference($id), $priority]);
        }
    }
}
