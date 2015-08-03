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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Form\Type\ReviewType');
    }

    function it_is_abstract_type_object()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('rating', 'choice', array(
                'choices' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5),
                'label' => 'sylius.form.review.rating.label',
                'expanded' => true,
                'multiple' => false,
            ))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('title', 'text', array(
                'label' => 'sylius.form.review.title.label'
            ))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('comment', 'textarea', array(
                'label' => 'sylius.form.review.comment.label'
            ))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $this->buildForm($builder, array('rating_steps' => 5));
    }

    function it_sets_default_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('rating_steps' => 5))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_review');
    }
}
