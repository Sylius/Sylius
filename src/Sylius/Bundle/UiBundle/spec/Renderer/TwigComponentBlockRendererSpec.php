<?php

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
            null,
            ['some' => 'context'],
            0,
            true,
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
            'some_component',
            ['some' => 'context'],
            0,
            true,
        );
        $componentRenderer->createAndRender('some_component', ['some' => 'context'])->willReturn('some_rendered_component');

        $this->render($someTemplateBlock, ['some' => 'context'])->shouldReturn('some_rendered_component');
    }
}
