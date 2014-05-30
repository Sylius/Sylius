<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class JobTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Form\Type\JobType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder->add('id', 'hidden')->shouldBeCalled()
            ->willReturn($builder);
        $builder->add('command')->shouldBeCalled()
            ->willReturn($builder);
        $builder->add('description')->shouldBeCalled()
            ->willReturn($builder);
        $builder->add('schedule')->shouldBeCalled()
            ->willReturn($builder);
        $builder->add('environment')->shouldBeCalled()
            ->willReturn($builder);
        $builder->add('serverType')->shouldBeCalled()
            ->willReturn($builder);
        $builder->add('priority', 'integer', array(
            'required' => false,
        ))->shouldBeCalled()
            ->willReturn($builder);
        $builder->add('active', 'checkbox', array(
            'required' => false,
        ))->shouldBeCalled()
            ->willReturn($builder);

        $this->buildForm($builder, array());
    }

    function it_should_define_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Sylius\Bundle\JobSchedulerBundle\Entity\Job'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
