<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslationMetadataFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.translatable.driver_chain')) {
            return;
        }

        // Find all the tagged services that are created by the AbstractResourceBundle
        $taggedServices = $container->findTaggedServiceIds(
            'sylius.translation_driver'
        );

        // Create an array with all the xml drivers
        $xmlDrivers = array();
        foreach ($taggedServices as $id => $attributes) {
             $xmlDrivers[] = new Reference($id);
        }

        // Add the array to Prezent driver chain (used by Prezent metadata factory)
        $container->getDefinition(
            'sylius.translatable.driver_chain'
        )->addArgument($xmlDrivers);

        // Add high priority to translatable listener
        $translatableListener = $container->getDefinition(
            'sylius.translatable.listener'
        );

//        TODO make sure this subscriber has higher priority than LoadORMMetadataSubscriber
//        It is absolutely necessary to have the translation metadata loaded before sylius resolves entity metadata
        $translatableListener->addTag('doctrine.event_subscriber', array('priority' => 99));
    }
}