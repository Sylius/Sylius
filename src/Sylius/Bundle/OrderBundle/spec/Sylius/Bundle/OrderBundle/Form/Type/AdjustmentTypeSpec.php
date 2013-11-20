<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AdjustmentTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Adjustment', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Form\Type\AdjustmentType');
    }

    function it_is_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_builds_form_with_proper_fields($builder)
    {
        $builder->add('label', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('description', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('amount', 'money', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_assigned_data_class($resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'Adjustment',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_adjustment');
    }
}
