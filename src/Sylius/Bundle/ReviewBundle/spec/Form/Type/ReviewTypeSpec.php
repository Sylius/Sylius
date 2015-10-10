<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('dataClass', array('validation_group'), 'subject');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Form\Type\ReviewType');
    }

    function it_is_abstract_type_object()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('rating', 'choice', array(
                'choices'  => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5),
                'label'    => 'sylius.form.review.rating',
                'expanded' => true,
                'multiple' => false,
            ))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('author', 'text', array(
                'label'    => 'sylius.form.review.author',
                'required' => false,
            ))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('title', 'text', array(
                'label' => 'sylius.form.review.title'
            ))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('comment', 'textarea', array(
                'label' => 'sylius.form.review.comment'
            ))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder->get('author')->willReturn($builder)->shouldBeCalled();
        $builder->addModelTransformer(Argument::type('Sylius\Bundle\ReviewBundle\Form\Transformer\ReviewerTransformer'))->willReturn($builder)->shouldBeCalled();

        $this->buildForm($builder, array('rating_steps' => 5));
    }

    function it_sets_default_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'rating_steps'      => 5,
                'validation_groups' => array('validation_group'),
            )
        )->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_subject_review');
    }
}
