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

namespace spec\Sylius\Bundle\UiBundle\Renderer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;
use Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface;
use Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;

final class DelegatingTemplateEventRendererSpec extends ObjectBehavior
{
    function let(TemplateBlockRegistryInterface $templateBlockRegistry, TemplateBlockRendererInterface $templateBlockRenderer): void
    {
        $this->beConstructedWith($templateBlockRegistry, $templateBlockRenderer);
    }

    function it_is_a_template_event_renderer(): void
    {
        $this->shouldImplement(TemplateEventRendererInterface::class);
    }

    function it_renders_template_events(
        TemplateBlockRegistryInterface $templateBlockRegistry,
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $firstTemplateBlock = new TemplateBlock('first_block', 'best_event_ever', 'firstBlock.txt.twig', [], 0, true);
        $secondTemplateBlock = new TemplateBlock('second_block', 'best_event_ever', 'secondBlock.txt.twig', [], 0, true);

        $templateBlockRegistry->findEnabledForEvents(['best_event_ever'])->willReturn([$firstTemplateBlock, $secondTemplateBlock]);

        $templateBlockRenderer->render($firstTemplateBlock, ['foo' => 'bar'])->willReturn('First block');
        $templateBlockRenderer->render($secondTemplateBlock, ['foo' => 'bar'])->willReturn('Second block');

        $this->render(['best_event_ever'], ['foo' => 'bar'])->shouldReturn("First block\nSecond block");
    }

    function it_returns_an_empty_string_if_no_blocks_are_found_for_an_event(
        TemplateBlockRegistryInterface $templateBlockRegistry,
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlockRegistry->findEnabledForEvents(['best_event_ever'])->willReturn([]);

        $templateBlockRenderer->render(Argument::cetera())->shouldNotBeCalled();

        $this->render(['best_event_ever'])->shouldReturn('');
    }
}
