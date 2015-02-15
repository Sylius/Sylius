<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\AttributeInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ArchetypeTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\ArchetypeTranslation');
    }

    function it_implements_Sylius_product_archetype_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Model\ArchetypeTranslationInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('T-Shirt size');
        $this->getName()->shouldReturn('T-Shirt size');
    }

    function it_has_fluent_interface(Collection $attributes, AttributeInterface $attribute)
    {
        $this->setName('T-Shirt size')->shouldReturn($this);
    }
}
