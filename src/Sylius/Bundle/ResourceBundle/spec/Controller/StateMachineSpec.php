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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\StateMachine;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface as ResourceStateMachineInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @mixin StateMachine
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StateMachineSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\StateMachine');
    }

    function it_implements_state_machine_interface()
    {
        $this->shouldImplement(ResourceStateMachineInterface::class);
    }

    function it_throws_an_exception_if_transition_is_not_defined(RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $requestConfiguration->hasStateMachine()->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('State machine must be configured to apply transition, check your routing.'))
            ->during('apply', [$requestConfiguration, $resource])
        ;
    }

    function it_applies_configured_state_machine_transition(
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $requestConfiguration->hasStateMachine()->willReturn(true);
        $requestConfiguration->getStateMachineGraph()->willReturn('sylius_product_review_state');
        $requestConfiguration->getStateMachineTransition()->willReturn('reject');

        $stateMachineFactory->get($resource, 'sylius_product_review_state')->willReturn($stateMachine);
        $stateMachine->apply('reject')->shouldBeCalled();

        $this->apply($requestConfiguration, $resource);
    }
}
