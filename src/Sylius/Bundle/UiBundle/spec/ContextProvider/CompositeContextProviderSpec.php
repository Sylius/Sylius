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

namespace spec\Sylius\Bundle\UiBundle\ContextProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\ContextProvider\Exception\NoSupportedContextProvider;
use Sylius\Bundle\UiBundle\Registry\Block;
use Webmozart\Assert\InvalidArgumentException;

final class CompositeContextProviderSpec extends ObjectBehavior
{
    function it_is_a_context_provider(): void
    {
        $this->beConstructedWith([]);

        $this->shouldImplement(ContextProviderInterface::class);
    }

    function it_throws_an_exception_when_any_of_passed_context_providers_is_not_a_context_provider(): void
    {
        $this->beConstructedWith([new \stdClass()]);

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_when_no_context_provider_supports_a_given_block(
        ContextProviderInterface $someContextProvider,
        Block $someBlock,
    ): void {
        $someBlock->getName()->willReturn('some_block');

        $someContextProvider->supports($someBlock)->willReturn(false);

        $this->beConstructedWith([$someContextProvider]);

        $this->shouldThrow(new NoSupportedContextProvider('No supported context provider found for block "some_block".'))->during('provide', [[], $someBlock]);
    }

    function it_returns_always_true_for_supports(Block $someBlock): void
    {
        $this->beConstructedWith([]);

        $this->supports($someBlock)->shouldReturn(true);
    }

    function it_returns_a_context_for_a_first_supported_context_provider(
        ContextProviderInterface $someContextProvider,
        ContextProviderInterface $anotherContextProvider,
        ContextProviderInterface $yetAnotherContextProvider,
        Block $someBlock,
    ): void {
        $someBlock->getName()->willReturn('some_block');

        $someContextProvider->supports($someBlock)->willReturn(false);

        $anotherContextProvider->supports($someBlock)->willReturn(true);
        $anotherContextProvider->provide(['foo' => 'bar'], $someBlock)->willReturn(['foo' => 'qux']);

        $yetAnotherContextProvider->supports($someBlock)->shouldNotBeCalled();

        $this->beConstructedWith([$someContextProvider, $anotherContextProvider]);

        $this->provide(['foo' => 'bar'], $someBlock)->shouldReturn(['foo' => 'qux']);
    }
}
