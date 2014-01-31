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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;
/**
 * @author Myke Hines <myke@webhines.com>
 */
class HistoryTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('History', array('sylius'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Form\Type\HistoryType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    public function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder->add('comment', 'textarea', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('notifyCustomer', 'checkbox', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'History',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }

    public function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_history');
    }
}
