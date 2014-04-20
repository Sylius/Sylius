<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SequenceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all generators.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class RegisterGeneratorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.generator_registry')) {
            return;
        }

        $generators = $container->getParameter('sylius.sequence.generators');
        $registry   = $container->getDefinition('sylius.generator_registry');

        foreach ($generators as $interface => $generator) {
            $registry->addMethodCall('addGenerator', array($interface, new Reference($generator)));
        }
    }
}
