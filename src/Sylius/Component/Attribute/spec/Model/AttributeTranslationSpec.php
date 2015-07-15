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

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AttributeTranslationSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Attribute\Model\AttributeTranslation');
    }

    public function it_implements_Sylius_attribute_interface()
    {
        $this->shouldImplement('Sylius\Component\Attribute\Model\AttributeTranslationInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_presentation_by_default()
    {
        $this->getPresentation()->shouldReturn(null);
    }

    public function its_presentation_is_mutable()
    {
        $this->setPresentation('Size');
        $this->getPresentation()->shouldReturn('Size');
    }

    public function it_has_fluent_interface()
    {
        $date = new \DateTime();

        $this->setPresentation('Brand')->shouldReturn($this);
    }
}
