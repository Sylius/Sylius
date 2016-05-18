<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Variation\Model\OptionInterface;
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class BuildVariantFormSubscriberSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $factory)
    {
        $this->beConstructedWith('variable_name', $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\EventListener\BuildVariantFormSubscriber');
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(
            [FormEvents::PRE_SET_DATA => 'preSetData']
        );
    }

    function it_adds_options_on_pre_set_data_event(
        $factory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $optionsForm,
        VariableInterface $variable,
        VariantInterface $variant,
        OptionValueInterface $optionValue,
        OptionInterface $options
    ) {
        $event->getForm()->shouldBeCalled()->willReturn($form);
        $event->getData()->shouldBeCalled()->willReturn($variant);

        $variant->getObject()->shouldBeCalled()->willReturn($variable);
        $variant->getOptions()->shouldBeCalled()->willReturn([$optionValue]);
        $variable->getOptions()->shouldBeCalled()->willReturn([$options]);
        $variable->hasOptions()->shouldBeCalled()->willReturn(true);

        $factory->createNamed(
            'options',
            'sylius_variable_name_option_value_collection',
            [$optionValue],
            [
                'options' => [$options],
                'auto_initialize' => false,
            ]
        )->shouldBeCalled()->willReturn($optionsForm);

        $form->add($optionsForm)->shouldBeCalled();

        $this->preSetData($event);
    }
}
