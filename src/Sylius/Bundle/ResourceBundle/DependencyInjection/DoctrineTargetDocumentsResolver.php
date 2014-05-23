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
 * Usable only with *doctrine/mongodb-odm* driver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class DoctrineTargetDocumentsResolver extends DoctrineTargetEntitiesResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(ContainerBuilder $container, array $interfaces)
    {
        if (!$container->hasDefinition('doctrine_mongodb.odm.listeners.resolve_target_document')) {
            throw new \RuntimeException('Cannot find Doctrine RTDL');
        }

        $resolveTargetDocumentListener = $container->findDefinition('doctrine_mongodb.odm.listeners.resolve_target_document');

        foreach ($interfaces as $interface => $model) {
            $resolveTargetDocumentListener
                ->addMethodCall('addResolveTargetDocument', array(
                    $this->getInterface($container, $interface),
                    $this->getClass($container, $model),
                    array()
                ))
            ;
        }

        if (!$resolveTargetDocumentListener->hasTag('doctrine_mongodb.odm.event_listener')) {
            $resolveTargetDocumentListener->addTag('doctrine_mongodb.odm.event_listener', array('event' => 'loadClassMetadata'));
        }
    }
}
