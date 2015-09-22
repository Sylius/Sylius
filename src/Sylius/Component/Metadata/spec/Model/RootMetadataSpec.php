<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\RootMetadataInterface;

/**
 * @mixin \Sylius\Component\Metadata\Model\RootMetadata
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class RootMetadataSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Model\RootMetadata');
    }

    function it_implements_Root_Metadata_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Model\RootMetadataInterface');
    }

    function it_has_id()
    {
        $this->getId()->shouldReturn(null);

        $this->setId('unique_key:42');
        $this->getId()->shouldReturn('unique_key:42');
    }

    function it_has_metadata(MetadataInterface $metadata)
    {
        $this->getMetadata()->shouldReturn(null);

        $this->setMetadata($metadata);
        $this->getMetadata()->shouldReturn($metadata);
    }
}
