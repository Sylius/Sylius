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
use Symfony\Component\Form\FormBuilder;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountConfigurationTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array('sylius'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\Rule\ItemCountConfigurationType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_should_build_form_with_count_field_and_equal_checkbox(FormBuilder $builder)
    {
        $builder
            ->add('count', 'integer', Argument::any())
            ->willReturn($builder)
        ;

        $builder
          ->add('equal', 'checkbox', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }
}
