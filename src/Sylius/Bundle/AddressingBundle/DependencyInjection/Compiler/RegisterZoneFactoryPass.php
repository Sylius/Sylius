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

use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class RegisterZoneFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.factory.zone')) {
            return;
        }

        $baseZoneFactoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('sylius.model.zone.class'),
            ]
        );

        $zoneMemberFactoryDefinition = new Definition(
            $container->getParameter('sylius.factory.zone_member.class'),
            [
                new Parameter('sylius.model.zone_member.class'),
            ]
        );

        $zoneFactoryDefinition = new Definition(
            $container->getParameter('sylius.factory.zone.class'),
            [
                $baseZoneFactoryDefinition,
                $zoneMemberFactoryDefinition,
            ]
        );

        $container->setDefinition('sylius.factory.zone', $zoneFactoryDefinition);
    }
}
