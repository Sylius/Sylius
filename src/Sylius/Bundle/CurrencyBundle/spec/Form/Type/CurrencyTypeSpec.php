<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CurrencyTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Currency', array('sylius'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('code', 'currency', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('exchangeRate', 'number', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('enabled', 'checkbox', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    public function it_should_define_assigned_data_class_and_validation_groups(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Currency',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_currency');
    }
}
