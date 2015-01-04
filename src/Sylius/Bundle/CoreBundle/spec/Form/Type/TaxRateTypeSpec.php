<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;

class TaxRateTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('TaxRate', array());
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\TaxRateType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_should_extend_Sylius_tax_rate_form_type()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Form\Type\TaxRateType');
    }
}
