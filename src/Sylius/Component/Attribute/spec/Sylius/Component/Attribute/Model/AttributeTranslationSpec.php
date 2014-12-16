<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Attribute\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\Model\AttributeTypes;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AttributeTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Attribute\Model\AttributeTranslation');
    }

    function it_implements_Sylius_attribute_interface()
    {
        $this->shouldImplement('Sylius\Component\Attribute\Model\AttributeTranslationInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_presentation_by_default()
    {
        $this->getPresentation()->shouldReturn(null);
    }

    function its_presentation_is_mutable()
    {
        $this->setPresentation('Size');
        $this->getPresentation()->shouldReturn('Size');
    }

    function it_has_fluent_interface()
    {
        $date = new \DateTime();

        $this->setPresentation('Brand')->shouldReturn($this);
    }
}
