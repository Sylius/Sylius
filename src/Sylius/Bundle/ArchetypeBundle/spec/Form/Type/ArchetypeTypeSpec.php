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
    public function let()
    {
        $this->beConstructedWith('Archetype', array('sylius'), 'book');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('code', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('translations', 'a2lix_translationsForms', Argument::any())
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

    public function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Archetype',
                'validation_groups' => array('sylius'),
            ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_book_archetype');
    }
}
