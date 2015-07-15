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
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gamil.com>
 */
class ItemCountConfigurationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\Rule\ItemCountConfigurationType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('count', 'integer', Argument::withKey('constraints'))->shouldBeCalled()->willReturn($builder);
        $builder->add('equal', 'checkbox', Argument::withKey('constraints'))->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }

    function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('sylius')
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_shipping_rule_item_count_configuration');
    }
}
