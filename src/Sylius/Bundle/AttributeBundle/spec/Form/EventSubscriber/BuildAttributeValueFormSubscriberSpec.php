<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeValueFormSubscriberSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $formFactory, RepositoryInterface $attributeRepository)
    {
        $this->beConstructedWith($formFactory, 'server', $attributeRepository);
    }

    function it_is_initialized()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\EventSubscriber\BuildAttributeValueFormSubscriber');
    }

    function it_subscribes_to_pre_set_data_event()
    {
        self::getSubscribedEvents()->shouldReturn(array('form.pre_set_data' => 'preSetData', 'form.pre_bind' => 'preSubmit'));
    }

    function it_is_triggered_pre_set_data_to_build_form_for_new_product_attribute(
        $formFactory,
        Form $form,
        Form $valueField,
        FormEvent $event
    ) {
        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($form);

        $formFactory->createNamed('value', 'sylius_attribute_type_text', null, Argument::type('array'))->willReturn($valueField);
        $form->add($valueField)->shouldBeCalled()->willReturn($form);

        $this->preSetData($event);
    }

    function it_is_triggered_pre_set_data_to_add_fields_base_on_product_attribute(
        $formFactory,
        AttributeInterface $productAttribute,
        AttributeValueInterface $productAttributeValue,
        Form $form,
        Form $valueField,
        FormEvent $event
    ) {
        $event->getData()->willReturn($productAttributeValue);
        $event->getForm()->willReturn($form);

        $productAttributeValue->getAttribute()->willReturn($productAttribute);
        $productAttribute->getType()->willReturn('text');
        $productAttribute->getName()->willReturn('Test');

        $productAttributeValue->getValue()->willReturn('Test');

        $formFactory->createNamed('value', 'sylius_attribute_type_text', 'Test', Argument::type('array'))->willReturn($valueField);

        $form->add($valueField)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_is_triggered_pre_submit_to_add_proper_typed_form_field(
        $attributeRepository,
        $formFactory,
        AttributeInterface $productAttribute,
        Form $form,
        Form $valueField,
        FormEvent $event
    ) {
        $event->getData()->willReturn(array(
            'attribute' => 1,
            'value' => array(
                'year'  => 2010,
                'month' => 01,
                'day'   => 01,
            ),
        ));
        $event->getForm()->willReturn($form);

        $attributeRepository->find(1)->willReturn($productAttribute);
        $productAttribute->getType()->willReturn('date');
        $productAttribute->getStorageType()->willReturn('date');

        $formFactory->createNamed('value', 'sylius_attribute_type_date', Argument::type('\DateTime'), Argument::type('array'))->willReturn($valueField);
        $form->add($valueField)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
