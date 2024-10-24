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

namespace spec\Sylius\Component\Core\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Checker\CLIContextCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CLIContextCheckerSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack): void
    {
        $this->beConstructedWith($requestStack);
    }

    function it_implements_command_based_context_checker_interface(): void
    {
        $this->shouldImplement(CLIContextCheckerInterface::class);
    }

    function it_returns_true_if_process_is_running_without_current_request(RequestStack $requestStack): void
    {
        $requestStack->getCurrentRequest()->willReturn(null);

        $this->isExecutedFromCLI()->shouldReturn(true);
    }

    function it_returns_false_if_process_is_running_with_current_request_defined(
        RequestStack $requestStack,
        Request $request,
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);

        $this->isExecutedFromCLI()->shouldReturn(false);
    }
}
