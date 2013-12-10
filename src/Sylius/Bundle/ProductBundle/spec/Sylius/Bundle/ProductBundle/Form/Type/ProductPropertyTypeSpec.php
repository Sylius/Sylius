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
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Router;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ProductPropertyTypeSpec extends ObjectBehavior
{
    function let(Router $router)
    {
        $this->beConstructedWith('ProductProperty', array('sylius'), $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Form\Type\ProductPropertyType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_property_types_prototype_and_pass_it_as_argument(
        FormBuilder $builder,
        FormFactoryInterface $formFactory
    )
    {
        $builder->getFormFactory()
            ->shouldBeCalled()
            ->willReturn($formFactory);

        $builder->add('property', 'sylius_property_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->addEventSubscriber(Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

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
