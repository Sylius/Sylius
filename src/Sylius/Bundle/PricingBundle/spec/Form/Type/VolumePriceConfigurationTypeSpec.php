<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PricingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class VolumePriceConfigurationTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Form\Type\VolumePriceConfigurationType');
    }

    public function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('min', 'number', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('max', 'number', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('price', 'sylius_money', Argument::type('array'))->shouldBeCalled();

        $this->buildForm($builder);
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_price_calculator_volume_based_configuration');
    }
}
