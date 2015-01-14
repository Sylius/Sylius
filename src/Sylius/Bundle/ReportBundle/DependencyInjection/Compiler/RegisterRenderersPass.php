<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterRenderersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.report.renderer')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.report.renderer');
        $renderers = array();

        foreach ($container->findTaggedServiceIds('sylius.report.renderer') as $id => $attributes) {
            if (!isset($attributes[0]['renderer']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged renderers needs to have `renderer` and `label` attributes.');
            }
            
            $name = $attributes[0]['renderer'];
            $renderers[$name] = $attributes[0]['label'];
            $registry->addMethodCall('register', array($name, new Reference($id)));
        }

        $container->setParameter('sylius.report.renderers', $renderers);
    }
}