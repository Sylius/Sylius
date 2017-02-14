<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdater;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdaterInterface;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ResourceUpdaterSpec extends ObjectBehavior
{
    function let(StateMachineInterface $stateMachine)
    {
        $this->beConstructedWith($stateMachine);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceUpdater::class);
    }

    function it_implements_a_resource_updater_interface()
    {
        $this->shouldImplement(ResourceUpdaterInterface::class);
    }

    function it_applies_state_machine_transition(
        StateMachineInterface $stateMachine,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
        $configuration->hasStateMachine()->willReturn(true);
        $stateMachine->apply($configuration, $resource)->shouldBeCalled();

        $manager->flush()->shouldBeCalled();

        $this->applyTransitionAndFlush($resource, $configuration, $manager);
    }

    function it_does_not_apply_state_machine_transition(
        StateMachineInterface $stateMachine,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
        $configuration->hasStateMachine()->willReturn(false);
        $stateMachine->apply($configuration, $resource)->shouldNotBeCalled();

        $manager->flush()->shouldBeCalled();

        $this->applyTransitionAndFlush($resource, $configuration, $manager);
    }
}
