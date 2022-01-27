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

namespace spec\Sylius\Component\Core\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Checker\CLIContextCheckerInterface;

final class CLIContextCheckerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('dev', ['test', 'test_cached']);
    }

    function it_implements_command_based_context_checker_interface(): void
    {
        $this->shouldImplement(CLIContextCheckerInterface::class);
    }

    function it_returns_true_if_process_is_not_running_in_test_environment_and_from_cli(): void
    {
        $this->isExecutedFromCLI()->shouldReturn(true);
    }

    function it_returns_false_if_process_is_running_in_test_environment_and_from_cli(): void
    {
        $this->beConstructedWith('test', ['test', 'test_cached']);

        $this->isExecutedFromCLI()->shouldReturn(false);
    }

    function it_returns_false_if_process_is_running_in_test_cached_environment_and_from_cli(): void
    {
        $this->beConstructedWith('test_cached', ['test', 'test_cached']);

        $this->isExecutedFromCLI()->shouldReturn(false);
    }
}
