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

    function it_proxies_render_property_to_Metadata_Renderer(
        MetadataAccessorInterface $metadataAccessor,
        MetadataRendererInterface $metadataRenderer,
        MetadataSubjectInterface $metadataSubject,
        MetadataInterface $metadata
    ) {
        $metadataRenderer->render($metadata)->shouldBeCalled()->willReturn('Rendered metadata');

        $metadataAccessor->getProperty($metadataSubject, 'property.path')->shouldBeCalled()->willReturn($metadata);

        $this->renderProperty($metadataSubject, 'property.path')->shouldReturn('Rendered metadata');
    }
}
