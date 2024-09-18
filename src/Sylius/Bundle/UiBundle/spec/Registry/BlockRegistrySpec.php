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
use Sylius\Bundle\UiBundle\Registry\BlockRegistryInterface;
use Sylius\Bundle\UiBundle\Registry\ComponentBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

final class BlockRegistrySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([]);
    }

    function it_is_a_block_registry(): void
    {
        $this->shouldImplement(BlockRegistryInterface::class);
    }

    function it_returns_all_template_blocks(): void
    {
        $templateBlock = new TemplateBlock('block_name', 'event', 'block.html.twig', [], 10, true, null);

        $this->beConstructedWith(['event' => ['block_name' => $templateBlock]]);

        $this->all()->shouldReturn(['event' => ['block_name' => $templateBlock]]);
    }

    function it_returns_enabled_blocks_for_a_given_event(): void
    {
        $firstBlock = new TemplateBlock('first_block', 'event', 'first.html.twig', [], 0, true, null);
        $secondBlock = new TemplateBlock('second_block', 'event', 'second.html.twig', [], 10, false, null);
        $thirdBlock = new TemplateBlock('third_block', 'event', 'third.html.twig', [], 50, true, null);
        $fourthBlock = new ComponentBlock('fourth_block', 'event', 'fourth.html.twig', [], [], 0, null);

        $this->beConstructedWith([
            'event' => [
                'first_block' => $firstBlock,
                'second_block' => $secondBlock,
            ],
            'another_event' => [
                'third_block' => $thirdBlock,
                'fourth_block' => $fourthBlock,
            ],
        ]);

        $this->findEnabledForEvents(['event'])->shouldReturn([$firstBlock]);
    }

    function it_returns_enabled_blocks_for_multiple_events(): void
    {
        $this->beConstructedWith([
            'generic_event' => ['block' => new TemplateBlock('block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 0, true)],
            'specific_event_template' => ['block' => new TemplateBlock('block', 'specific_event_template', 'specific.html.twig', null, null, null, null)],
            'specific_event_context' => ['block' => new TemplateBlock('block', 'specific_event_context', null, ['other' => 'context'], null, null, null)],
            'specific_event_priority' => ['block' => new TemplateBlock('block', 'specific_event_priority', null, null, 10, null, null)],
            'specific_event_enabled' => ['block' => new TemplateBlock('block', 'specific_event_enabled', null, null, null, false, null)],
        ]);

        $this->findEnabledForEvents(['specific_event_template', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('block', 'specific_event_template', 'specific.html.twig', ['foo' => 'bar'], 0, true, null),
        ]);
        $this->findEnabledForEvents(['specific_event_context', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('block', 'specific_event_context', 'generic.html.twig', ['other' => 'context'], 0, true, null),
        ]);
        $this->findEnabledForEvents(['specific_event_priority', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('block', 'specific_event_priority', 'generic.html.twig', ['foo' => 'bar'], 10, true, null),
        ]);
        $this->findEnabledForEvents(['specific_event_enabled', 'generic_event'])->shouldReturn([]);
        $this->findEnabledForEvents(['specific_event_priority', 'specific_event_template', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('block', 'specific_event_priority', 'specific.html.twig', ['foo' => 'bar'], 10, true, null),
        ]);
    }

    function it_returns_enabled_blocks_sorted_by_priority_for_multiple_events(): void
    {
        $this->beConstructedWith([
            'generic_event' => [
                'first_block' => new TemplateBlock('first_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 50, true, null),
                'third_block' => new TemplateBlock('third_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], -10, true, null),
                'second_block' => new TemplateBlock('second_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 0, true, null),
                'invisible_block' => new TemplateBlock('invisible_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 0, false, null),
            ],
            'specific_event' => [
                'additional_block' => new TemplateBlock('additional_block', 'specific_event', 'specific.html.twig', [], 75, true, null),
                'second_block' => new TemplateBlock('second_block', 'specific_event', null, null, null, false, null),
                'third_block' => new TemplateBlock('third_block', 'specific_event', null, null, 100, null, null),
                'invisible_block' => new TemplateBlock('invisible_block', 'specific_event', null, [], null, null, null),
            ],
        ]);

        $this->findEnabledForEvents(['specific_event', 'generic_event'])->shouldIterateLike([
            new TemplateBlock('third_block', 'specific_event', 'generic.html.twig', ['foo' => 'bar'], 100, true, null),
            new TemplateBlock('additional_block', 'specific_event', 'specific.html.twig', [], 75, true, null),
            new TemplateBlock('first_block', 'generic_event', 'generic.html.twig', ['foo' => 'bar'], 50, true, null),
        ]);
    }
}
