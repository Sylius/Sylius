<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PricingBundle\Form\Extension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Pricing\Calculator\VolumeBasedCalculator;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class PriceableTypeExtensionSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $calculatorRegistry, EventSubscriberInterface $subscriber)
    {
        $this->beConstructedWith('pricing_form', $calculatorRegistry, $subscriber);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Form\Extension\PriceableTypeExtension');
    }

    function it_builds_form(
        $calculatorRegistry,
        $subscriber,
        FormBuilderInterface $builder,
        VolumeBasedCalculator $calculator,
        FormBuilderInterface $formBuilder,
        FormInterface $form
    ) {
        $builder->addEventSubscriber($subscriber)->shouldBeCalled()->willreturn($builder);
        $builder->add('pricingCalculator', 'sylius_price_calculator_choice', Argument::type('array'))->shouldBeCalled();

        $calculatorRegistry->all()->shouldBeCalled()->willReturn(['type' => $calculator]);
        $calculator->getType()->shouldBeCalled()->willReturn('standard');

        $builder->create('pricingConfiguration', 'sylius_price_calculator_standard')
            ->shouldBeCalled()
            ->willReturn($formBuilder);

        $formBuilder->getForm()->shouldBeCalled()->willReturn($form);

        $builder->setAttribute('prototypes', ['type' => $form])->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    function it_builds_view(FormView $view, FormInterface $form, FormConfigInterface $formConfig)
    {
        $form->getConfig()->shouldBeCalled()->willReturn($formConfig);
        $formConfig->getAttribute('prototypes')->shouldBeCalled()->willReturn(['type' => $form]);

        $form->createView($view)->shouldBeCalled();

        $this->buildView($view, $form, []);
    }

    function it_extends_a_form_type()
    {
        $this->getExtendedType()->shouldReturn('pricing_form');
    }
}
