<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Product\Model\AttributeInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class AttributeValueTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('AttributeValue', array('sylius'), 'server');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_attribute_types_prototype_and_passes_it_as_argument(
        FormBuilder $builder,
        FormBuilder $fieldBuilder,
        FormFactoryInterface $formFactory,
        ChoiceListInterface $choiceList,
        AttributeInterface $attribute
    )
    {
        $builder->getFormFactory()->willReturn($formFactory);
        $builder->add('attribute', 'sylius_server_attribute_choice', Argument::any())->willReturn($builder);

        $builder
            ->addEventSubscriber(Argument::any())
            ->willReturn($builder)
        ;

        $attribute->getType()->willReturn('checkbox')->shouldBeCalled();
        $attribute->getConfiguration()->willReturn(array('label' => 'Some label'))->shouldBeCalled();

        $choiceList
            ->getChoices()
            ->willReturn(array($attribute))
        ;
        $fieldBuilder
            ->getOption('choice_list')
            ->willReturn($choiceList)
        ;
        $builder
            ->get('attribute')
            ->willReturn($fieldBuilder)
        ;
        $builder
            ->create('value', 'checkbox', array('label' => 'Some label'))
            ->shouldBeCalled()
            ->willReturn($fieldBuilder)
        ;
        $fieldBuilder->getForm()->willReturn('Form for attribute');

        $builder->setAttribute('prototypes', array(0 => 'Form for attribute'))->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'AttributeValue', 'validation_groups' => array('sylius')))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_server_attribute_value');
    }
}
