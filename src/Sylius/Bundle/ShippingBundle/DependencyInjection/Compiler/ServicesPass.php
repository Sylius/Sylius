<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Modifies resource services after initialization.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $shippingMethod = $container->getDefinition('sylius.form.type.shipping_method');
        $shippingMethod->addArgument(new Reference('sylius.shipping_calculator_registry'));
        $shippingMethod->addArgument(new Reference('sylius.shipping_rule_checker_registry'));

        $shippingMethod = $container->getDefinition('sylius.form.type.shipping_method_rule');
        $shippingMethod->addArgument(new Reference('sylius.shipping_rule_checker_registry'));
    }
}
