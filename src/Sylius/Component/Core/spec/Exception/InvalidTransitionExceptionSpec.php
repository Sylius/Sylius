<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Exception;

use PhpSpec\ObjectBehavior;
use SM\SMException;
use SM\StateMachine\StateMachineInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class InvalidTransitionExceptionSpec extends ObjectBehavior
{
    function let(StateMachineInterface $stateMachine)
    {
        $stateMachine->getGraph()->willReturn('checkout_state_machine');
        $stateMachine->getPossibleTransitions()->willReturn(['start', 'abandon']);

        $this->beConstructedWith('start_checkout', $stateMachine);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Exception\InvalidTransitionException');
    }

    function it_is_sm_exception()
    {
        $this->shouldHaveType(SMException::class);
    }

    function it_has_message()
    {
        $this
            ->getMessage()
            ->shouldReturn('Transition "start_checkout" is invalid for "checkout_state_machine" state machine. Possible transitions are: start and abandon.')
        ;
    }
}
