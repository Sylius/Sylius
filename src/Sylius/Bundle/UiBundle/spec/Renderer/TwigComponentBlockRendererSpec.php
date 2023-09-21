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
use Symfony\UX\TwigComponent\ComponentRendererInterface;

final class TwigComponentBlockRendererSpec extends ObjectBehavior
{
    function let(TemplateBlockRendererInterface $decoratedRenderer, ComponentRendererInterface $componentRenderer): void
    {
        $this->beConstructedWith($decoratedRenderer, $componentRenderer);
    }

    function it_invokes_decorated_renderer_when_no_component_is_set(
        TemplateBlockRendererInterface $decoratedRenderer,
    ): void {
        $someTemplateBlock = new TemplateBlock(
            'some_name',
            'some_event_name',
            'some_template',
            ['some' => 'context'],
            0,
            true,
            null,
        );
        $decoratedRenderer->render($someTemplateBlock, ['some' => 'context'])->shouldBeCalled();

        $this->render($someTemplateBlock, ['some' => 'context']);
    }

    function it_renders_a_component_when_component_is_set(
        ComponentRendererInterface $componentRenderer,
    ): void {
        $someTemplateBlock = new TemplateBlock(
            'some_name',
            'some_event_name',
            'some_template',
            ['another' => 'value'],
            0,
            true,
            'some_component',
        );
        $componentRenderer
            ->createAndRender('some_component', ['context' => ['another' => 'value', 'some' => 'value']])
            ->willReturn('some_rendered_component')
        ;

        $this->render($someTemplateBlock, ['some' => 'value'])->shouldReturn('some_rendered_component');
    }
}
