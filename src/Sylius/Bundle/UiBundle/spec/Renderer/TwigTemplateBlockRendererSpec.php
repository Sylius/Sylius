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
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface;
use Twig\Environment;

final class TwigTemplateBlockRendererSpec extends ObjectBehavior
{
    function let(
        Environment $twig,
        ContextProviderInterface $firstContextProvider,
        ContextProviderInterface $secondContextProvider,
    ): void {
        $this->beConstructedWith($twig, [$firstContextProvider, $secondContextProvider]);

        $firstContextProvider->supports(Argument::type(TemplateBlock::class))->willReturn(true);
        $secondContextProvider->supports(Argument::type(TemplateBlock::class))->willReturn(false);
    }

    function it_is_a_template_block_renderer(): void
    {
        $this->shouldImplement(TemplateBlockRendererInterface::class);
    }

    function it_renders_a_template_block(
        Environment $twig,
        ContextProviderInterface $firstContextProvider,
        ContextProviderInterface $secondContextProvider,
    ): void {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'block.txt.twig', ['sample' => 'Hi', 'switch' => true], 0, true);

        $twig->render('block.txt.twig', ['sample' => 'Hello', 'switch' => true])->willReturn('Block content');

        $firstContextProvider
            ->provide(['sample' => 'Hello', 'switch' => true], $templateBlock)
            ->willReturn(['sample' => 'Hello', 'switch' => true])
            ->shouldBeCalled()
        ;

        $secondContextProvider->provide(Argument::any())->shouldNotBeCalled();

        $this->render($templateBlock, ['sample' => 'Hello', 'switch' => true])->shouldReturn('Block content');
    }
}
