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

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Registry\Block;
use Sylius\Bundle\UiBundle\Renderer\BlockRendererInterface;
use Sylius\Bundle\UiBundle\Renderer\Exception\NoSupportedBlockRenderer;
use Sylius\Bundle\UiBundle\Renderer\SupportableBlockRendererInterface;

final class CompositeBlockRendererSpec extends ObjectBehavior
{
    function it_is_a_block_renderer(): void
    {
        $this->beConstructedWith([]);

        $this->shouldImplement(BlockRendererInterface::class);
    }

    function it_throws_an_exception_when_any_of_passed_block_renderers_is_not_a_block_renderer(): void
    {
        $this->beConstructedWith([new \stdClass()]);

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_when_no_block_renderers_supports_a_given_block(
        SupportableBlockRendererInterface $someBlockRenderer,
        Block $someBlock,
    ): void {
        $someBlock->getName()->willReturn('some_block');

        $someBlockRenderer->supports($someBlock)->willReturn(false);

        $this->beConstructedWith([$someBlockRenderer]);

        $this
            ->shouldThrow(new NoSupportedBlockRenderer('No supported block renderer found for "some_block" block.'))
            ->during('render', [$someBlock, []])
        ;
    }

    function it_renders_a_block_using_a_first_supported_block_renderer(
        SupportableBlockRendererInterface $someBlockRenderer,
        SupportableBlockRendererInterface $anotherBlockRenderer,
        SupportableBlockRendererInterface $yetAnotherBlockRenderer,
        Block $someBlock,
    ): void {
        $someBlock->getName()->willReturn('some_block');

        $someBlockRenderer->supports($someBlock)->willReturn(false);

        $anotherBlockRenderer->supports($someBlock)->willReturn(true);
        $anotherBlockRenderer->render($someBlock, [])->willReturn('Rendered block');

        $yetAnotherBlockRenderer->supports($someBlock)->shouldNotBeCalled();

        $this->beConstructedWith([$someBlockRenderer, $anotherBlockRenderer, $yetAnotherBlockRenderer]);

        $this->render($someBlock, [])->shouldReturn('Rendered block');
    }
}
