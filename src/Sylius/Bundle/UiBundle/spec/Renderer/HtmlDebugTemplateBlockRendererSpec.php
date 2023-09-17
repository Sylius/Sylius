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

    function it_does_not_render_a_debug_html_comment_if_the_template_block_is_not_a_component_nor_a_twig_template(
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'some_content', null, null, true, null);

        $templateBlockRenderer->render($templateBlock, [])->willReturn('Rendered template');

        $this->render($templateBlock, [])->shouldReturn('Rendered template');
    }

    function it_renders_a_debug_html_comment_if_the_template_block_has_a_configured_component(
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'some_content', null, null, true, 'component_name');

        $templateBlockRenderer->render($templateBlock, [])->willReturn('Rendered template');

        $this->render($templateBlock, [])->shouldReturn(
            '<!-- BEGIN BLOCK | event name: "event_name", block name: "block_name", component: "component_name", priority: 0 -->' . "\n" .
            'Rendered template' . "\n" .
            '<!-- END BLOCK | event name: "event_name", block name: "block_name" -->'
        );
    }

    function it_renders_a_debug_html_comment_if_the_template_block_has_a_configured_twig_template(
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'template.html.twig', [], null, true, null);

        $templateBlockRenderer->render($templateBlock, [])->willReturn('Rendered template');

        $this->render($templateBlock, [])->shouldReturn(
            '<!-- BEGIN BLOCK | event name: "event_name", block name: "block_name", template: "template.html.twig", priority: 0 -->' . "\n" .
            'Rendered template' . "\n" .
            '<!-- END BLOCK | event name: "event_name", block name: "block_name" -->'
        );
    }
}
