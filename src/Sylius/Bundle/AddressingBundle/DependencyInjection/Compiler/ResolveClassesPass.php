<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Resolves model classes.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResolveClassesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $driver = $container->getParameter('sylius_addressing.driver');

        if (SyliusResourceBundle::DRIVER_DOCTRINE_ORM !== $driver) {
            return;
        }

        $resolveTargetEntityListener = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');

        $resolveTargetEntityListener
            ->addMethodCall('addResolveTargetEntity', array(
                'Sylius\Bundle\AddressingBundle\Model\AddressInterface', $container->getParameter('sylius_addressing.model.address.class'), array()
            ))
            ->addMethodCall('addResolveTargetEntity', array(
                'Sylius\Bundle\AddressingBundle\Model\CountryInterface', $container->getParameter('sylius_addressing.model.country.class'), array()
            ))
            ->addMethodCall('addResolveTargetEntity', array(
                'Sylius\Bundle\AddressingBundle\Model\ProvinceInterface', $container->getParameter('sylius_addressing.model.province.class'), array()
            ))
        ;

        if (!$resolveTargetEntityListener->hasTag('doctrine.event_listener')) {
            $resolveTargetEntityListener->addTag('doctrine.event_listener', array('event' => 'loadClassMetadata'));
        }
    }
}
