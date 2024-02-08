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
use Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface;

final class HtmlDebugTemplateBlockRendererSpec extends ObjectBehavior
{
    function let(TemplateBlockRendererInterface $templateBlockRenderer): void
    {
        $this->beConstructedWith($templateBlockRenderer);
    }

    function it_is_a_template_block_renderer(): void
    {
        $this->shouldImplement(TemplateBlockRendererInterface::class);
    }

    function it_renders_html_debug_comment_prepending_the_block_if_rendering_html_template(
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlockRenderer->render(Argument::cetera())->willReturn('Block content');

        $this->render(
            new TemplateBlock('block_name', 'event_name', 'block.html.twig', [], 0, true),
            ['foo' => 'bar'],
        )->shouldReturn(
            '<!-- BEGIN BLOCK | event name: "event_name", block name: "block_name", template: "block.html.twig", priority: 0 -->' . "\n" .
            'Block content' . "\n" .
            '<!-- END BLOCK | event name: "event_name", block name: "block_name" -->',
        );
    }

    function it_does_not_render_html_debug_comment_prepending_the_block_if_rendering_non_html_template(
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlockRenderer->render(Argument::cetera())->willReturn('Block content');

        $this->render(
            new TemplateBlock('block_name', 'event_name', 'block.txt.twig', [], 0, true),
            ['foo' => 'bar'],
        )->shouldReturn('Block content');
    }
}
