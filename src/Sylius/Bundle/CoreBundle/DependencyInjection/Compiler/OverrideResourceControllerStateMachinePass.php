<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Sylius\Bundle\CoreBundle\Resource\StateMachine\Controller\CompositeStateMachine;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideResourceControllerStateMachinePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $stateMachineDefinition = $container->register('sylius.resource_controller.state_machine', CompositeStateMachine::class);
        $stateMachineDefinition->setPublic(false);
        $stateMachineDefinition->addArgument(new Reference('sylius_abstraction.state_machine'));
    }
}
