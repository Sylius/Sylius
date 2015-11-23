<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Sylius\Component\Rbac\Authorization\Voter\DelegatingVoterInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class VotersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $voterId = $container->getParameter('sylius.rbac.voter_id');
        $voterDefinition = $container->getDefinition($voterId);
        $this->addResourceVoters($container, $voterDefinition);

        $bridgeDefinition = $container->getDefinition('sylius.rbac.voter.bridge');
        $bridgeDefinition->addMethodCall('setRbacVoter', array(new Reference($voterId)));

        if (!$container->hasDefinition('sylius.rbac.voter')) {
            $container->setAlias('sylius.rbac.voter', 'sylius.rbac.voter.bridge');
        }

        if (!$container->findDefinition('sylius.rbac.voter')->hasTag('security.voter')) {
            $container->findDefinition('sylius.rbac.voter')->addTag('security.voter');
        }
    }

    private function addResourceVoters(ContainerBuilder $container, Definition $voterDefinition)
    {
        $voterClass = $voterDefinition->getClass();

        if ($container->hasParameter(substr($voterClass, 1, -1))) {
            $voterClass = $container->getParameter(substr($voterClass, 1, -1));
        }

        if (!in_array(DelegatingVoterInterface::class, class_implements($voterClass))) {
            return;
        }

        $resourceVoters = $container->findTaggedServiceIds('rbac.resource_voter');

        foreach (array_keys($resourceVoters) as $id) {
            $voterDefinition->addMethodCall('addResourceVoter', array(new Reference($id)));
        }
    }
}
