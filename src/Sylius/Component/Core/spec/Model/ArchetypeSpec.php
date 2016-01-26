<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;

/**
 * @mixin \Sylius\Component\Core\Model\Archetype
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ArchetypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Archetype');
    }

    function it_implements_Sylius_Core_Archetype_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\ArchetypeInterface');
    }

    function it_has_metadata_class_identifier()
    {
        $this->getMetadataClassIdentifier()->shouldReturn('Archetype');
    }
}
