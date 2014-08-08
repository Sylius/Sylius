<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class BuildAttributeValueFormListenerSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $formFactory)
    {
        $this->beConstructedWith($formFactory);
    }

    function it_subscribes_to_pre_set_data_event()
    {
        self::getSubscribedEvents()->shouldReturn(array('form.pre_set_data' => 'buildForm'));
    }

    function it_builds_form_with_attribute_and_value_when_new_product_attribute(
        FormEvent $event,
        Form $form,
        Form $valueField,
        $formFactory
    ) {
        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($form);

        $formFactory->createNamed('value', 'text', null, Argument::any())->willReturn($valueField)->shouldBeCalled();
        $form->add($valueField)->shouldBeCalled()->willReturn($form);

        $this->buildForm($event);
    }

    function it_builds_value_field_base_on_product_attribute(
        FormEvent $event,
        Form $form,
        AttributeValueInterface $productAttribute,
        Form $valueField,
        $formFactory
    ) {
        $productAttribute->getType()->willReturn('checkbox');
        $productAttribute->getName()->willReturn('My name');
        $productAttribute->getConfiguration()->willReturn(array());

        $event->getData()->willReturn($productAttribute);
        $event->getForm()->willReturn($form);

        $formFactory->createNamed('value', 'checkbox', null, array('label' => 'My name', 'auto_initialize' => false))->willReturn($valueField)->shouldBeCalled();

        $form->remove('attribute')->shouldBeCalled()->willReturn($form);
        $form->add($valueField)->shouldBeCalled()->willReturn($form);

        $this->buildForm($event);
    }

    function it_builds_options_base_on_product_attribute(
        FormEvent $event,
        Form $form,
        AttributeValueInterface $productAttribute,
        Form $valueField,
        $formFactory
    ) {
        $productAttribute->getType()->willReturn('choice');
        $productAttribute->getConfiguration()->willReturn(array(
            'choices' => array(
                'red'  => 'Red',
                'blue' => 'Blue'
            )
        ));
        $productAttribute->getName()->willReturn('My name');

        $event->getData()->willReturn($productAttribute);
        $event->getForm()->willReturn($form);

        $formFactory
            ->createNamed(
                'value',
                'choice',
                null,
                array('label' => 'My name', 'auto_initialize' => false, 'choices' => array('red' => 'Red', 'blue' => 'Blue'))
            )
            ->willReturn($valueField)
            ->shouldBeCalled()
        ;

        $form->remove('attribute')->shouldBeCalled()->willReturn($form);
        $form->add($valueField)->shouldBeCalled()->willReturn($form);

        $this->buildForm($event);
    }
}
