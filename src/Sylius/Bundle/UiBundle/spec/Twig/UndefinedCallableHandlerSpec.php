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

namespace spec\Sylius\Bundle\UiBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Twig\UndefinedCallableHandler;
use Twig\Error\SyntaxError;

final class UndefinedCallableHandlerSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(UndefinedCallableHandler::class);
    }

    function it_returns_false_on_non_handled_undefined_function(): void
    {
        $this::onUndefinedFunction('undefined_function')->shouldReturn(false);
    }

    function it_throws_a_syntax_error_on_handled_undefined_function(): void
    {
        $this->shouldThrow(SyntaxError::class)->during('onUndefinedFunction', ['sylius_grid_render_field']);
    }

    function it_returns_false_on_non_handled_undefined_filter(): void
    {
        $this::onUndefinedFilter('undefined_filter')->shouldReturn(false);
    }

    function it_throws_a_syntax_error_on_handled_undefined_filter(): void
    {
        $this->shouldThrow(SyntaxError::class)->during('onUndefinedFilter', ['sylius_currency_symbol']);
    }
}
