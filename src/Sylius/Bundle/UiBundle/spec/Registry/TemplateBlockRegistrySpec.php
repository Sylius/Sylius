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
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;

final class TemplateBlockRegistrySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([]);
    }

    function it_is_a_template_block_registry(): void
    {
        $this->shouldImplement(TemplateBlockRegistryInterface::class);
    }

    function it_returns_all_template_blocks(): void
    {
        $templateBlock = new TemplateBlock('block_name', 'event', 'block.html.twig', [], 10, true);

        $this->beConstructedWith(['event' => ['block_name' => $templateBlock]]);

        $this->all()->shouldReturn(['event' => ['block_name' => $templateBlock]]);
    }

    function it_returns_enabled_template_blocks_for_a_given_event(): void
    {
        $firstTemplateBlock = new TemplateBlock('first_block', 'event', 'first.html.twig', [], 0, true);
        $secondTemplateBlock = new TemplateBlock('second_block', 'event', 'second.html.twig', [], 10, false);
        $thirdTemplateBlock = new TemplateBlock('third_block', 'event', 'third.html.twig', [], 50, true);

        $this->beConstructedWith([
            'event' => [
                'first_block' => $firstTemplateBlock,
                'second_block' => $secondTemplateBlock,
            ],
            'another_event' => [
                'third_block' => $thirdTemplateBlock,
            ],
        ]);

        $this->findEnabledForEvents(['event'])->shouldReturn([$firstTemplateBlock]);
    }

    function it_returns_enabled_template_blocks_for_multiple_events(): void
    {
        $this->beConstructedWith([
            'generic_event' => ['block' => new TemplateBlock('block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 0, true)],
            'specific_event_template' => ['block' => new TemplateBlock('block', 'specific_event_template', 'specific.html.twig', null, null, null)],
            'specific_event_context' => ['block' => new TemplateBlock('block', 'specific_event_context', null, ['other' => 'context'], null, null)],
            'specific_event_priority' => ['block' => new TemplateBlock('block', 'specific_event_priority', null, null, 10, null)],
            'specific_event_enabled' => ['block' => new TemplateBlock('block', 'specific_event_enabled', null, null, null, false)],
        ]);

        $this->findEnabledForEvents(['specific_event_template', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('block', 'specific_event_template', 'specific.html.twig', ['foo' => 'bar'], 0, true),
        ]);
        $this->findEnabledForEvents(['specific_event_context', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('block', 'specific_event_context', 'generic.html.twig', ['other' => 'context'], 0, true),
        ]);
        $this->findEnabledForEvents(['specific_event_priority', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('block', 'specific_event_priority', 'generic.html.twig', ['foo' => 'bar'], 10, true),
        ]);
        $this->findEnabledForEvents(['specific_event_enabled', 'generic_event'])->shouldReturn([]);
        $this->findEnabledForEvents(['specific_event_priority', 'specific_event_template', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('block', 'specific_event_priority', 'specific.html.twig', ['foo' => 'bar'], 10, true),
        ]);
    }

    function it_returns_enabled_template_blocks_sorted_by_priority_for_multiple_events(): void
    {
        $this->beConstructedWith([
            'generic_event' => [
                'first_block' => new TemplateBlock('first_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 50, true),
                'third_block' => new TemplateBlock('third_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], -10, true),
                'second_block' => new TemplateBlock('second_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 0, true),
                'invisible_block' => new TemplateBlock('invisible_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 0, false),
            ],
            'specific_event' => [
                'additional_block' => new TemplateBlock('additional_block', 'specific_event', 'specific.html.twig', [], 75, true),
                'second_block' => new TemplateBlock('second_block', 'specific_event', null, null, null, false),
                'third_block' => new TemplateBlock('third_block', 'specific_event', null, null, 100, null),
                'invisible_block' => new TemplateBlock('invisible_block', 'specific_event', null, [], null, null),
            ],
        ]);

        $this->findEnabledForEvents(['specific_event', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('third_block', 'specific_event', 'generic.html.twig', ['foo' => 'bar'], 100, true),
            new TemplateBlock('additional_block', 'specific_event', 'specific.html.twig', [], 75, true),
            new TemplateBlock('first_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 50, true),
        ]);
    }
}
