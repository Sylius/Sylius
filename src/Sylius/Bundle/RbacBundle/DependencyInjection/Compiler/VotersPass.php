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
use Symfony\Component\DependencyInjection\DefinitionDecorator;

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
        $voterId = $container->getParameter('sylius.rbac.voter.id');

        $voterDefinition = $container->getDefinition($voterId);
        $this->addResourceVoters($container, $voterDefinition);

        $bridgeInstance = new DefinitionDecorator('sylius.rbac.voter.bridge');
        $bridgeInstance->addMethodCall('setRbacVoter', array(new Reference($voterId)));
        $bridgeInstance->addTag('security.voter');

        $container->setDefinition(sprintf('%s.bridge', $voterId), $bridgeInstance);
    }

    private function addResourceVoters(ContainerBuilder $container, Definition $voterDefinition)
    {
        $resourceVoters = $container->findTaggedServiceIds('rbac.resource_voter');

        foreach (array_keys($resourceVoters) as $id) {
            $voterDefinition->addMethodCall('addResourceVoter', array(new Reference($id)));
        }
    }
}
