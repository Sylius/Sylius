<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Form\Type\Rule;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Test\FormBuilderInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gamil.com>
 */
class UnitCountConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\Rule\UnitCountConfigurationType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('count', 'integer', Argument::withKey('constraints'))->shouldBeCalled()->willReturn($builder);
        $builder->add('equal', 'checkbox', Argument::withKey('constraints'))->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_shipping_rule_unit_count_configuration');
    }
}
