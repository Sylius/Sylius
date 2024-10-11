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

final class ComponentBlockSpec extends ObjectBehavior
{
    function it_represents_a_component_block(): void
    {
        $this->beConstructedWith('block_name', 'event_name', 'MyComponent', ['my' => 'input'], ['my' => 'context'], 10, false);

        $this->getName()->shouldReturn('block_name');
        $this->getEventName()->shouldReturn('event_name');
        $this->getComponentName()->shouldReturn('MyComponent');
        $this->getComponentInputs()->shouldReturn(['my' => 'input']);
        $this->getContext()->shouldReturn(['my' => 'context']);
        $this->getPriority()->shouldReturn(10);
        $this->isEnabled()->shouldReturn(false);
    }

    function it_overwrites_a_component_block_with_an_another_component_block(): void
    {
        $this->beConstructedWith('block_name', 'event_name', 'MyComponent', ['my' => 'input'], ['my' => 'context'], 10, false);

        $this
            ->overwriteWith(
                new ComponentBlock(
                    'block_name',
                    'specific_event_name',
                    'MyAnotherComponent',
                    ['my' => 'another_input'],
                    ['my' => 'another_context'],
                    20,
                    true,
                ),
            )
            ->shouldBeLike(
                new ComponentBlock(
                    'block_name',
                    'specific_event_name',
                    'MyAnotherComponent',
                    ['my' => 'another_input'],
                    ['my' => 'another_context'],
                    20,
                    true,
                ),
            )
        ;
    }

    function it_throws_an_exception_if_trying_to_overwrite_with_a_differently_named_block(): void
    {
        $this->beConstructedWith('block_name', 'event_name', 'MyComponent', ['my' => 'input'], ['my' => 'context'], 10, false);

        $this->shouldThrow(\DomainException::class)->during(
            'overwriteWith',
            [
                new ComponentBlock(
                    'another_block_name',
                    'specific_event_name',
                    'MyComponent',
                    [],
                    [],
                    0,
                    false,
                ),
            ],
        );
    }

    function it_throws_an_exception_when_trying_to_override_template_block_with_template_block(): void
    {
        $this->beConstructedWith('block_name', 'event_name', 'MyComponent', ['my' => 'input'], ['my' => 'context'], 10, false);

        $exceptionMessage = sprintf(
            'Trying to overwrite component block "%s" with block of different type "%s".',
            'block_name',
            'Sylius\Bundle\UiBundle\Registry\TemplateBlock',
        );

        $this
            ->shouldThrow(new \DomainException($exceptionMessage))
            ->during('overwriteWith', [new TemplateBlock('block_name', 'specific_event_name', null, null, null, true)])
        ;
    }
}
