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
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class BuildAttributeValueFormListenerSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $formFactory, ServiceRegistryInterface $attributeTypeRegistry)
    {
        $this->beConstructedWith($formFactory, $attributeTypeRegistry, 'server');
    }

    function it_subscribes_to_pre_set_data_event()
    {
        self::getSubscribedEvents()->shouldReturn(array('form.pre_set_data' => 'buildForm'));
    }

    function it_builds_form_with_attribute_and_value_for_new_product_attribute(
        $formFactory,
        Form $form,
        Form $valueField,
        FormEvent $event
    ) {
        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($form);

        $formFactory->createNamed('value', 'sylius_attribute_type_text', null, Argument::type('array'))->willReturn($valueField);
        $form->add($valueField)->shouldBeCalled()->willReturn($form);

        $this->buildForm($event);
    }

    function it_builds_value_field_base_on_product_attribute(
        $attributeTypeRegistry,
        $formFactory,
        AttributeInterface $productAttribute,
        AttributeTypeInterface $productAttributeType,
        AttributeValueInterface $productAttributeValue,
        Form $form,
        Form $valueField,
        FormEvent $event
    ) {
        $event->getData()->willReturn($productAttributeValue);
        $event->getForm()->willReturn($form);

        $productAttributeValue->getAttribute()->willReturn($productAttribute);
        $productAttribute->getType()->willReturn('text');

        $attributeTypeRegistry->get('text')->willReturn($productAttributeType);
        $productAttributeType->getFormType()->willReturn('text_form');
        $productAttributeValue->getValue()->willReturn('Test');

        $formFactory->createNamed('value', 'text_form', array('value' => 'Test'), Argument::type('array'))->willReturn($valueField);

        $form->add($valueField)->shouldBeCalled();

        $this->buildForm($event);
    }
}
