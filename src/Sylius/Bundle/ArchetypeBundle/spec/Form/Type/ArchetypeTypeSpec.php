<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ArchetypeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Archetype', array('sylius'), 'book');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('code', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('name', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('parent', 'sylius_book_archetype_choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('attributes', 'sylius_book_attribute_choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('options', 'sylius_book_option_choice', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Archetype',
                'validation_groups' => array('sylius')
            ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_book_archetype');
    }
}
