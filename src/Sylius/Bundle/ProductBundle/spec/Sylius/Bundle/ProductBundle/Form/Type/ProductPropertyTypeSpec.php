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

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ProductPropertyTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ProductProperty', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Form\Type\ProductPropertyType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder                                   $builder
     * @param Symfony\Component\Form\FormBuilder                                   $fieldBuilder
     * @param Symfony\Component\Form\FormFactoryInterface                          $formFactory
     * @param Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface $choiceList
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface                  $property
     */
    function it_builds_property_types_prototype_and_pass_it_as_argument(
        $builder, $fieldBuilder, $formFactory, $choiceList, $property
    )
    {
        $builder->getFormFactory()->willReturn($formFactory);
        $builder->add('property', 'sylius_property_choice', Argument::any())->willReturn($builder);

        $builder
            ->addEventSubscriber(Argument::any())
            ->willReturn($builder)
        ;

        $property->getType()->willReturn('checkbox')->shouldBeCalled();
        $property->getConfiguration()->willReturn(array('label' => 'Some label'))->shouldBeCalled();

        $choiceList
            ->getChoices()
            ->willReturn(array($property))
        ;
        $fieldBuilder
            ->getOption('choice_list')
            ->willReturn($choiceList)
        ;
        $builder
            ->get('property')
            ->willReturn($fieldBuilder)
        ;
        $builder
            ->create('value', 'checkbox', array('label' => 'Some label'))
            ->shouldBeCalled()
            ->willReturn($fieldBuilder)
        ;
        $fieldBuilder->getForm()->willReturn('form for property');

        $builder->setAttribute('prototypes', array(0 => 'form for property'))->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'ProductProperty', 'validation_groups' => array('sylius')))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_product_property');
    }
}
