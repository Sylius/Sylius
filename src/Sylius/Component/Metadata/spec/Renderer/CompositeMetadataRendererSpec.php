<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Renderer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;

/**
 * @mixin \Sylius\Component\Metadata\Renderer\CompositeMetadataRenderer
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CompositeMetadataRendererSpec extends ObjectBehavior
{
    function let(MetadataRendererInterface $firstRenderer, MetadataRendererInterface $secondRenderer)
    {
        $this->beConstructedWith([$firstRenderer, $secondRenderer]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Renderer\CompositeMetadataRenderer');
    }

    function it_implements_Metadata_Renderer_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Renderer\MetadataRendererInterface');
    }

    function it_check_if_given_renderer_supports_a_metadata(
        MetadataRendererInterface $firstRenderer,
        MetadataRendererInterface $secondRenderer,
        MetadataInterface $supportedMetadata,
        MetadataInterface $unsupportedMetadata
    ) {
        $firstRenderer->supports($supportedMetadata, ['foo' => 'bar'])->shouldBeCalled()->willReturn(false);
        $secondRenderer->supports($supportedMetadata, ['foo' => 'bar'])->shouldBeCalled()->willReturn(true);

        $firstRenderer->supports($unsupportedMetadata, ['bar' => 'foo'])->shouldBeCalled()->willReturn(false);
        $secondRenderer->supports($unsupportedMetadata, ['bar' => 'foo'])->shouldBeCalled()->willReturn(false);

        $this->supports($supportedMetadata, ['foo' => 'bar'])->shouldReturn(true);
        $this->supports($unsupportedMetadata, ['bar' => 'foo'])->shouldReturn(false);
    }

    function it_delegates_rendering_to_correct_renderer(
        MetadataRendererInterface $firstRenderer,
        MetadataRendererInterface $secondRenderer,
        MetadataInterface $metadata
    ) {
        $firstRenderer->supports($metadata, ['foo' => 'bar'])->shouldBeCalled()->willReturn(false);
        $secondRenderer->supports($metadata, ['foo' => 'bar'])->shouldBeCalled()->willReturn(true);

        $firstRenderer->render(Argument::cetera())->shouldNotBeCalled();
        $secondRenderer->render($metadata, ['foo' => 'bar'])->shouldBeCalled()->willReturn('Rendered metadata');

        $this->render($metadata, ['foo' => 'bar'])->shouldReturn('Rendered metadata');
    }

    function it_throws_exception_if_trying_to_render_unsupported_metadata(
        MetadataRendererInterface $firstRenderer,
        MetadataRendererInterface $secondRenderer,
        MetadataInterface $metadata
    ) {
        $firstRenderer->supports($metadata, ['bar' => 'baz'])->shouldBeCalled()->willReturn(false);
        $secondRenderer->supports($metadata, ['bar' => 'baz'])->shouldBeCalled()->willReturn(false);

        $firstRenderer->render(Argument::cetera())->shouldNotBeCalled();
        $secondRenderer->render(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow('\InvalidArgumentException')->duringRender($metadata, ['bar' => 'baz']);
    }
}
