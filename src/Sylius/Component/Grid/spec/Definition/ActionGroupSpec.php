<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Definition;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\Action;

/**
 * @mixin ActionGroup
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ActionGroupSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('named', ['row']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\Definition\ActionGroup');
    }

    function it_has_code()
    {
        $this->getName()->shouldReturn('row');
    }

    function it_does_not_have_any_actions_by_default()
    {
        $this->getActions()->shouldReturn([]);
    }

    function it_can_have_action_definitions(Action $action)
    {
        $action->getName()->willReturn('display_summary');
        
        $this->addAction($action);
        $this->getAction('display_summary')->shouldReturn($action);
        $this->getActions()->shouldReturn(['display_summary' => $action]);
    }

    function it_cannot_have_two_actions_with_the_same_name(Action $firstAction, Action $secondAction)
    {
        $firstAction->getName()->willReturn('read_book');
        $secondAction->getName()->willReturn('read_book');

        $this->addAction($firstAction);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('addAction', [$secondAction])
        ;
    }
    
    function it_knows_if_action_with_given_name_already_exists(Action $action)
    {
        $action->getName()->willReturn('read_book');
        $this->addAction($action);
        
        $this->hasAction('read_book')->shouldReturn(true);
        $this->hasAction('delete_book')->shouldReturn(false);
    }
}
