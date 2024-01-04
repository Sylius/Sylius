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
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\ComponentBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\UX\TwigComponent\ComponentRendererInterface;

final class TwigComponentBlockRendererSpec extends ObjectBehavior
{
    function let(
        ComponentRendererInterface $componentRenderer,
        ContextProviderInterface $contextProvider,
        ExpressionLanguage $expressionLanguage,
    ): void {
        $this->beConstructedWith(
            $componentRenderer,
            $contextProvider,
            $expressionLanguage,
        );
    }

    function it_returns_true_if_block_is_supported(): void
    {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'template.html.twig', [], 0, true);
        $componentBlock = new ComponentBlock('block_name', 'event_name', 'Component', [], [], 0, true);

        $this->supports($templateBlock)->shouldReturn(false);
        $this->supports($componentBlock)->shouldReturn(true);
    }

    function it_throws_an_exception_when_trying_to_render_unsupported_block(): void
    {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'template.html.twig', [], 0, true);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('render', [$templateBlock])
        ;
    }

    function it_renders_component_block(
        ComponentRendererInterface $componentRenderer,
        ContextProviderInterface $contextProvider,
        ExpressionLanguage $expressionLanguage,
    ): void {
        $componentBlock = new ComponentBlock(
            'block_name',
            'event_name',
            'Component',
            [
                'foo' => 'bar',
                'bar' => 'expr:foo',
                'nested' => [
                    'foo' => 'expr:bar',
                    'bar' => 'expr:baz',
                ],
            ],
            [],
            0,
            true,
        );

        $context = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'qux',
        ];

        $contextProvider->provide([], $componentBlock)->willReturn($context);

        $expressionLanguage->evaluate('foo', ['context' => $context])->willReturn('bar');
        $expressionLanguage->evaluate('bar', ['context' => $context])->willReturn('baz');
        $expressionLanguage->evaluate('baz', ['context' => $context])->willReturn('qux');

        $componentRenderer
            ->createAndRender('Component', [
                'foo' => 'bar',
                'bar' => 'bar',
                'nested' => [
                    'foo' => 'baz',
                    'bar' => 'qux',
                ],
            ])
            ->willReturn('rendered_component')
        ;

        $this->render($componentBlock)->shouldReturn('rendered_component');
    }
}
