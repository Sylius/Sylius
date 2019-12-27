<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\UiBundle\Renderer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface;
use Twig\Environment;

final class TemplateBlockRendererSpec extends ObjectBehavior
{
    function let(Environment $twig): void
    {
        $this->beConstructedWith($twig, true);
    }

    function it_is_a_template_block_renderer(): void
    {
        $this->shouldImplement(TemplateBlockRendererInterface::class);
    }

    function it_renders_a_template_block(Environment $twig): void
    {
        $twig->render('block.txt.twig', ['foo' => 'bar'])->willReturn('Block content');

        $this->render(
            'event_name',
            new TemplateBlock('block_name', 'block.txt.twig', 0, true),
            ['foo' => 'bar']
        )->shouldReturn('Block content');
    }

    function it_renders_html_debug_comment_prepending_the_comment_if_in_debug_mode_rendering_html_template(Environment $twig): void
    {
        $this->beConstructedWith($twig, true);

        $twig->render('block.html.twig', ['foo' => 'bar'])->willReturn('Block content');

        $this->render(
            'event_name',
            new TemplateBlock('block_name', 'block.html.twig', 0, true),
            ['foo' => 'bar']
        )->shouldReturn(
            '<!-- event name: "event_name", block name: "block_name", template: "block.html.twig", priority: 0 -->' . "\n" .
            'Block content'
        );
    }

    function it_renders_html_debug_comment_prepending_the_comment_if_not_in_debug_mode_rendering_html_template(Environment $twig): void
    {
        $this->beConstructedWith($twig, false);

        $twig->render('block.html.twig', ['foo' => 'bar'])->willReturn('Block content');

        $this->render(
            'event_name',
            new TemplateBlock('block_name', 'block.html.twig', 0, true),
            ['foo' => 'bar']
        )->shouldReturn('Block content');
    }
}
