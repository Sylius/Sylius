<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SupportBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class CategoryTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('SupportTranslation', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SupportBundle\Form\Type\CategoryTranslationType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('title', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'data_class'        => 'SupportTranslation',
                    'validation_groups' => array('sylius')
                )
            )
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_support_category_translation');
    }
}
