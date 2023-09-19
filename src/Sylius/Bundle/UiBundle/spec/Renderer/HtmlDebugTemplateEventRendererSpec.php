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
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;
use Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;

final class HtmlDebugTemplateEventRendererSpec extends ObjectBehavior
{
    function let(TemplateEventRendererInterface $templateEventRenderer, TemplateBlockRegistryInterface $templateBlockRegistry): void
    {
        $this->beConstructedWith($templateEventRenderer, $templateBlockRegistry);
    }

    function it_is_a_template_event_renderer(): void
    {
        $this->shouldImplement(TemplateEventRendererInterface::class);
    }

    function it_does_not_render_html_debug_comments_when_there_are_no_template_blocks_with_a_defined_component_nor_template(
        TemplateEventRendererInterface $templateEventRenderer,
        TemplateBlockRegistryInterface $templateBlockRegistry
    ): void {
        $templateBlockRegistry->findEnabledForEvents(['event_name'])->willReturn([
            new TemplateBlock('some_block_one', 'some_event', 'some content', null, null, true, null),
        ]);

        $templateEventRenderer->render(['event_name'], [])->willReturn('rendered_content');

        $this->render(['event_name'])->shouldReturn('rendered_content');
    }

    function it_renders_html_debug_comment_when_no_template_block_passed(
        TemplateEventRendererInterface $templateEventRenderer,
        TemplateBlockRegistryInterface $templateBlockRegistry
    ): void {
        $templateBlockRegistry->findEnabledForEvents(['event_name'])->willReturn([]);

        $templateEventRenderer->render(['event_name'], [])->willReturn('rendered_content');

        $this->render(['event_name'])->shouldReturn(
            '<!-- BEGIN EVENT | event name: "event_name" -->' . "\n" .
            'rendered_content' . "\n" .
            '<!-- END EVENT | event name: "event_name" -->'
        );
    }

    function it_renders_html_debug_comment_when_at_least_one_block_has_a_configured_component(
        TemplateEventRendererInterface $templateEventRenderer,
        TemplateBlockRegistryInterface $templateBlockRegistry
    ): void {
        $templateBlockRegistry->findEnabledForEvents(['event_name'])->willReturn([
            new TemplateBlock('some_block_one', 'some_event', 'some content',  null, null, true, 'SomeComponent'),
        ]);

        $templateEventRenderer->render(['event_name'], [])->willReturn('rendered_content');

        $this->render(['event_name'])->shouldReturn(
            '<!-- BEGIN EVENT | event name: "event_name" -->' . "\n" .
            'rendered_content' . "\n" .
            '<!-- END EVENT | event name: "event_name" -->'
        );
    }

    function it_renders_html_debug_comment_when_at_least_one_block_has_a_configured_twig_template(
        TemplateEventRendererInterface $templateEventRenderer,
        TemplateBlockRegistryInterface $templateBlockRegistry
    ): void {
        $templateBlockRegistry->findEnabledForEvents(['event_name'])->willReturn([
            new TemplateBlock('some_block_one', 'some_event', 'some_template.html.twig', null, null, true, null),
        ]);

        $templateEventRenderer->render(['event_name'], [])->willReturn('rendered_content');

        $this->render(['event_name'])->shouldReturn(
            '<!-- BEGIN EVENT | event name: "event_name" -->' . "\n" .
            'rendered_content' . "\n" .
            '<!-- END EVENT | event name: "event_name" -->'
        );
    }
}
