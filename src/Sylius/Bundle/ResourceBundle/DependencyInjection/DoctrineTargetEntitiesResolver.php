<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Resolves given target entities with container parameters.
 * Usable only with *doctrine/orm* driver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class DoctrineTargetEntitiesResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(ContainerBuilder $container, array $interfaces)
    {
        if (!$container->hasDefinition('doctrine.orm.listeners.resolve_target_entity')) {
            throw new \RuntimeException('Cannot find Doctrine RTEL');
        }

        $resolveTargetEntityListener = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');

        foreach ($interfaces as $interface => $parameter) {
            if (!$container->hasParameter($parameter)) {
                continue;
            }

            $resolveTargetEntityListener
                ->addMethodCall('addResolveTargetEntity', array(
                    $interface, $container->getParameter($parameter), array()
                ))
            ;
        }

        if (!$resolveTargetEntityListener->hasTag('doctrine.event_listener')) {
            $resolveTargetEntityListener->addTag('doctrine.event_listener', array('event' => 'loadClassMetadata'));
        }
    }
}
