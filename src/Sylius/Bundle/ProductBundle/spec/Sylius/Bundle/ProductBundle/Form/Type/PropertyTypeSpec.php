<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Model\PropertyTypes;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Leszek Prabucki <leszek.prabucki@gmail.pl>
 */
class PropertyTypeSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Form\FormBuilder          $builder
     * @param Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    function let($builder, $formFactory)
    {
        $this->beConstructedWith('Property', array('sylius'));

        $builder->getFormFactory()->willReturn($formFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Form\Type\PropertyType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_builds_form_with_proper_fields($builder)
    {
        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\ProductBundle\Form\EventListener\BuildPropertyFormChoicesListener'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('name', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('presentation', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('type', 'choice', array('choices' => PropertyTypes::getChoices()))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Property', 'validation_groups' => array('sylius')))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_property');
    }
}
