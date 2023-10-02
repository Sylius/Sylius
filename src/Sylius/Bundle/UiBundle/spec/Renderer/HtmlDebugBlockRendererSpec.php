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
use Sylius\Bundle\UiBundle\Registry\ComponentBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Renderer\BlockRendererInterface;

final class HtmlDebugBlockRendererSpec extends ObjectBehavior
{
    function let(BlockRendererInterface $templateBlockRenderer): void
    {
        $this->beConstructedWith($templateBlockRenderer);
    }

    function it_is_a_block_renderer(): void
    {
        $this->shouldImplement(BlockRendererInterface::class);
    }

    function it_does_not_render_html_debug_comment_if_template_block_is_not_an_html_twig_file_path(
        BlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'template', [], 0, true);

        $templateBlockRenderer->render($templateBlock, [])->willReturn('template');

        $this->render($templateBlock)->shouldReturn('template');
    }

    function it_renders_html_debug_comment_if_template_block_is_an_html_twig_file_path(
        BlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'template.html.twig', [], 0, true);

        $templateBlockRenderer->render($templateBlock, [])->willReturn('template');

        $this->render($templateBlock)->shouldReturn(
            '<!-- BEGIN BLOCK | event name: "event_name", block name: "block_name", template: "template.html.twig", priority: 0 -->' . "\n" .
            'template' . "\n" .
            '<!-- END BLOCK | event name: "event_name", block name: "block_name" -->',
        );
    }

    function it_renders_html_debug_comment_if_the_block_is_a_component(
        BlockRendererInterface $templateBlockRenderer,
    ): void {
        $componentBlock = new ComponentBlock('block_name', 'event_name', 'Component', [], [], 0, true);

        $templateBlockRenderer->render($componentBlock, ['some' => 'context'])->willReturn('template');

        $this->render($componentBlock, ['some' => 'context'])->shouldReturn(
            '<!-- BEGIN BLOCK | event name: "event_name", block name: "block_name", component: "Component", priority: 0 -->' . "\n" .
            'template' . "\n" .
            '<!-- END BLOCK | event name: "event_name", block name: "block_name" -->',
        );
    }
}
