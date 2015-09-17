<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Twig;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Metadata\Accessor\MetadataAccessorInterface;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;

/**
 * @mixin \Sylius\Component\Metadata\Twig\MetadataExtension
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataExtensionSpec extends ObjectBehavior
{
    function let(MetadataAccessorInterface $metadataAccessor, MetadataRendererInterface $metadataRenderer)
    {
        $this->beConstructedWith($metadataAccessor, $metadataRenderer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Twig\MetadataExtension');
    }

    function it_is_Twig_extension()
    {
        $this->shouldHaveType('\Twig_Extension');
    }

    function it_proxies_get_property_to_Metadata_Accessor(
        MetadataAccessorInterface $metadataAccessor,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataAccessor->getProperty($metadataSubject, 'property.path[0]')->shouldBeCalled()->willReturn('property value');

        $this->getProperty($metadataSubject, 'property.path[0]')->shouldReturn('property value');
    }

    function it_proxies_get_property_to_Metadata_Accessor_even_when_no_property_path_has_been_passed(
        MetadataAccessorInterface $metadataAccessor,
        MetadataSubjectInterface $metadataSubject,
        MetadataInterface $metadata
    ) {
        $metadataAccessor->getProperty($metadataSubject, null)->shouldBeCalled()->willReturn($metadata);

        $this->getProperty($metadataSubject, null)->shouldReturn($metadata);
    }

    function it_proxies_get_property_to_Metadata_Accessor_and_uses_default_value_if_null_was_returned(
        MetadataAccessorInterface $metadataAccessor,
        MetadataSubjectInterface $metadataSubject,
        MetadataInterface $metadata
    ) {
        $metadataAccessor->getProperty($metadataSubject, 'property.path[0]')->shouldBeCalled()->willReturn(null);

        $this->getProperty($metadataSubject, 'property.path[0]', $metadata)->shouldReturn($metadata);
    }

    function it_proxies_render_property_to_Metadata_Renderer(
        MetadataAccessorInterface $metadataAccessor,
        MetadataRendererInterface $metadataRenderer,
        MetadataSubjectInterface $metadataSubject,
        MetadataInterface $metadata
    ) {
        $metadataRenderer->render($metadata, ['foo' => 'bar'])->shouldBeCalled()->willReturn('Rendered property metadata');

        $metadataAccessor->getProperty($metadataSubject, 'property.path')->shouldBeCalled()->willReturn($metadata);

        $this->renderProperty($metadataSubject, 'property.path', ['foo' => 'bar'])->shouldReturn('Rendered property metadata');
    }

    function it_proxies_render_property_to_Metadata_Renderer_even_when_no_property_path_has_been_passed(
        MetadataAccessorInterface $metadataAccessor,
        MetadataRendererInterface $metadataRenderer,
        MetadataSubjectInterface $metadataSubject,
        MetadataInterface $metadata
    ) {
        $metadataRenderer->render($metadata, ['foo' => 'bar'])->shouldBeCalled()->willReturn('Rendered metadata');

        $metadataAccessor->getProperty($metadataSubject, null)->shouldBeCalled()->willReturn($metadata);

        $this->renderProperty($metadataSubject, null, ['foo' => 'bar'])->shouldReturn('Rendered metadata');
    }

    function it_does_not_proxy_render_property_to_Metadata_Renderer_if_there_is_no_metadata(
        MetadataAccessorInterface $metadataAccessor,
        MetadataRendererInterface $metadataRenderer,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataRenderer->render(Argument::cetera())->shouldNotBeCalled();

        $metadataAccessor->getProperty($metadataSubject, 'property')->shouldBeCalled()->willReturn(null);

        $this->renderProperty($metadataSubject, 'property')->shouldReturn(null);
    }
}
