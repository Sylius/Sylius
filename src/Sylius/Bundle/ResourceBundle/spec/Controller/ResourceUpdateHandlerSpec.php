<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandler;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ResourceUpdateHandlerSpec extends ObjectBehavior
{
    function let(StateMachineInterface $stateMachine): void
    {
        $this->beConstructedWith($stateMachine);
    }

    function it_implements_a_resource_update_handler_interface(): void
    {
        $this->shouldImplement(ResourceUpdateHandlerInterface::class);
    }

    function it_applies_a_state_machine_transition(
        StateMachineInterface $stateMachine,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ): void {
        $configuration->hasStateMachine()->willReturn(true);
        $stateMachine->apply($configuration, $resource)->shouldBeCalled();

        $manager->flush()->shouldBeCalled();

        $this->handle($resource, $configuration, $manager);
    }

    function it_does_not_apply_a_state_machine_transition(
        StateMachineInterface $stateMachine,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ): void {
        $configuration->hasStateMachine()->willReturn(false);
        $stateMachine->apply($configuration, $resource)->shouldNotBeCalled();

        $manager->flush()->shouldBeCalled();

        $this->handle($resource, $configuration, $manager);
    }
}
