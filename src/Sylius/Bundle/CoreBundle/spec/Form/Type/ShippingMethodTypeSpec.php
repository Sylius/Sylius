<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormSubscriber;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ShippingMethodTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $calculatorRegistry, ServiceRegistryInterface $checkerRegistry, FormRegistryInterface $formRegistry)
    {
        $this->beConstructedWith('ShippingMethod', ['sylius'], $calculatorRegistry, $checkerRegistry, $formRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ShippingMethodType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_should_extend_Sylius_shipping_method_form_type()
    {
        $this->shouldHaveType(ShippingMethodType::class);
    }

    function it_builds_form_with_proper_fields($calculatorRegistry, $checkerRegistry, FormBuilderInterface $builder, FormFactoryInterface $formFactory)
    {
        $calculatorRegistry->all()->willReturn([]);
        $checkerRegistry->all()->willReturn([]);

        $builder->getFormFactory()->willReturn($formFactory);

        $builder
            ->addEventSubscriber(Argument::type(BuildShippingMethodFormSubscriber::class))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(Argument::type(AddCodeFormSubscriber::class))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('translations', 'sylius_translations', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('category', 'sylius_shipping_category_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('categoryRequirement', 'choice', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('calculator', 'sylius_shipping_calculator_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('enabled', 'checkbox', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('zone', 'sylius_zone_choice', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('taxCategory', 'sylius_tax_category_choice', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder->setAttribute(Argument::any(), Argument::any())->shouldBeCalled();

        $this->buildForm($builder, []);
    }
}
