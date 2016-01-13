<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Accessor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\Provider\MetadataProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @mixin \Sylius\Component\Metadata\Accessor\MetadataAccessor
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataAccessorSpec extends ObjectBehavior
{
    function let(MetadataProviderInterface $metadataProvider, PropertyAccessorInterface $propertyAccessor)
    {
        $this->beConstructedWith($metadataProvider, $propertyAccessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Accessor\MetadataAccessor');
    }

    function it_implements_Metadata_Helper_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Accessor\MetadataAccessorInterface');
    }

    function it_returns_given_metadata_property_if_it_exists(
        MetadataProviderInterface $metadataProvider,
        PropertyAccessorInterface $propertyAccessor,
        MetadataSubjectInterface $metadataSubject,
        MetadataInterface $metadata
    ) {
        $propertyAccessor->getValue($metadata, 'property.path[0]')->shouldBeCalled()->willReturn('property value');

        $metadataProvider->findMetadataBySubject($metadataSubject)->shouldBeCalled()->willReturn($metadata);

        $this->getProperty($metadataSubject, 'property.path[0]');
    }
}
