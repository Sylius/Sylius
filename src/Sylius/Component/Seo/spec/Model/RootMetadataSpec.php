<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Seo\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Seo\Model\MetadataInterface;
use Sylius\Component\Seo\Model\RootMetadataInterface;

/**
 * @mixin \Sylius\Component\Seo\Model\RootMetadata
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class RootMetadataSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Seo\Model\RootMetadata');
    }

    function it_implements_Root_Metadata_interface()
    {
        $this->shouldImplement('Sylius\Component\Seo\Model\RootMetadataInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_key()
    {
        $this->getKey()->shouldReturn(null);

        $this->setKey('unique_key:42');
        $this->getKey()->shouldReturn('unique_key:42');
    }

    function it_has_metadata(MetadataInterface $metadata)
    {
        $this->getMetadata()->shouldReturn(null);

        $this->setMetadata($metadata);
        $this->getMetadata()->shouldReturn($metadata);
    }

    function it_has_parent(RootMetadataInterface $rootMetadata)
    {
        $this->getParent()->shouldReturn(null);
        $this->hasParent()->shouldReturn(false);

        $this->setParent($rootMetadata);

        $this->getParent()->shouldReturn($rootMetadata);
        $this->hasParent()->shouldReturn(true);
    }

    function it_has_children(RootMetadataInterface $rootMetadata)
    {
        $this->getChildren()->shouldHaveCount(0);

        $this->addChild($rootMetadata);

        $this->getChildren()->shouldHaveCount(1);

        $this->removeChild($rootMetadata);

        $this->getChildren()->shouldHaveCount(0);
    }
}
