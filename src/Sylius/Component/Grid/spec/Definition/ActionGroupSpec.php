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

namespace spec\Sylius\Component\Grid\Definition;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Action;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ActionGroupSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('named', ['row']);
    }

    function it_has_code(): void
    {
        $this->getName()->shouldReturn('row');
    }

    function it_does_not_have_any_actions_by_default(): void
    {
        $this->getActions()->shouldReturn([]);
    }

    function it_can_have_action_definitions(Action $action): void
    {
        $action->getName()->willReturn('display_summary');

        $this->addAction($action);
        $this->getAction('display_summary')->shouldReturn($action);
        $this->getActions()->shouldReturn(['display_summary' => $action]);
    }

    function it_cannot_have_two_actions_with_the_same_name(Action $firstAction, Action $secondAction): void
    {
        $firstAction->getName()->willReturn('read_book');
        $secondAction->getName()->willReturn('read_book');

        $this->addAction($firstAction);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('addAction', [$secondAction])
        ;
    }

    function it_knows_if_action_with_given_name_already_exists(Action $action): void
    {
        $action->getName()->willReturn('read_book');
        $this->addAction($action);

        $this->hasAction('read_book')->shouldReturn(true);
        $this->hasAction('delete_book')->shouldReturn(false);
    }
}
