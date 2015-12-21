<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormTypeInterface;

class ZoneTypeChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\ZoneTypeChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_zone_type_choice');
    }

    function it_has_a_parent_type()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
