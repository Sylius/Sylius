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

namespace spec\Sylius\Bundle\UiBundle\Registry;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Registry\ComponentBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

final class TemplateBlockSpec extends ObjectBehavior
{
    function it_represents_a_template_block(): void
    {
        $this->beConstructedWith('block_name', 'event_name', 'block.html.twig', ['foo' => 'bar'], 10, false);

        $this->getName()->shouldReturn('block_name');
        $this->getEventName()->shouldReturn('event_name');
        $this->getTemplate()->shouldReturn('block.html.twig');
        $this->getContext()->shouldReturn(['foo' => 'bar']);
        $this->getPriority()->shouldReturn(10);
        $this->isEnabled()->shouldReturn(false);
    }

    function it_overwrites_a_template_block_with_an_another_template_block(): void
    {
        $this->beConstructedWith('block_name', 'event_name', 'block.html.twig', ['foo' => 'bar'], 10, false);

        $this
            ->overwriteWith(new TemplateBlock('block_name', 'specific_event_name', 'another.html.twig', null, null, null))
            ->shouldBeLike(new TemplateBlock('block_name', 'specific_event_name', 'another.html.twig', ['foo' => 'bar'], 10, false))
        ;

        $this
            ->overwriteWith(new TemplateBlock('block_name', 'specific_event_name', null, [], null, null))
            ->shouldBeLike(new TemplateBlock('block_name', 'specific_event_name', 'block.html.twig', [], 10, false))
        ;

        $this
            ->overwriteWith(new TemplateBlock('block_name', 'specific_event_name', null, null, -5, null))
            ->shouldBeLike(new TemplateBlock('block_name', 'specific_event_name', 'block.html.twig', ['foo' => 'bar'], -5, false))
        ;

        $this
            ->overwriteWith(new TemplateBlock('block_name', 'specific_event_name', null, null, null, true))
            ->shouldBeLike(new TemplateBlock('block_name', 'specific_event_name', 'block.html.twig', ['foo' => 'bar'], 10, true))
        ;
    }

    function it_throws_an_exception_if_trying_to_overwrite_with_a_differently_named_block(): void
    {
        $this->beConstructedWith('block_name', 'event_name', 'block.html.twig', ['foo' => 'bar'], 10, false);

        $this->shouldThrow(\DomainException::class)->during('overwriteWith', [new TemplateBlock('different_name', 'specific_event_name', null, null, null, null)]);
    }

    function it_has_sensible_defaults(): void
    {
        $this->beConstructedWith('block_name', 'event_name', null, null, null, null);

        $this->shouldThrow(\DomainException::class)->during('getTemplate');
        $this->getContext()->shouldReturn([]);
        $this->getPriority()->shouldReturn(0);
        $this->isEnabled()->shouldReturn(true);
    }

    function it_throws_an_exception_when_trying_to_override_template_block_with_component_block(): void
    {
        $this->beConstructedWith('block_name', 'event_name', 'block.html.twig', ['foo' => 'bar'], 10, false);

        $exceptionMessage = sprintf(
            'Trying to overwrite template block "%s" with block of different type "%s".',
            'block_name',
            'Sylius\Bundle\UiBundle\Registry\ComponentBlock',
        );

        $this
            ->shouldThrow(new \DomainException($exceptionMessage))
            ->during('overwriteWith', [new ComponentBlock('block_name', 'specific_event_name', 'MyComponent', [], [], 0, false)])
        ;
    }
}
