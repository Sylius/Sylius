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
use Sylius\Bundle\AttributeBundle\AttributeType\CheckboxAttributeType;
use Sylius\Bundle\AttributeBundle\AttributeType\DateAttributeType;
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
    
    function it_throws_an_exception_on_pre_set_data_event_when_attribute_value_is_undefined(FormEvent $event)
    {
        $event->getData()->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('preSetData', array($event))
        ;
    }

    function it_adds_a_value_form_field_with_correct_type_based_on_the_attribute(
        $formFactory,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue,
        Form $form,
        Form $valueField,
        FormEvent $event
    ) {
        $event->getData()->willReturn($attributeValue);
        $event->getForm()->willReturn($form);

        $attributeValue->getAttribute()->willReturn($attribute);
        $attributeValue->getValue()->willReturn(true);
        $attribute->getType()->willReturn(CheckboxAttributeType::TYPE);
        $attribute->getName()->willReturn('Is promoted?');

        $formFactory->createNamed('value', 'sylius_attribute_type_checkbox', true, Argument::type('array'))->willReturn($valueField);

        $form->add($valueField)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_throws_an_exception_on_pre_submit_event_when_attribute_id_is_undefined(FormEvent $event)
    {
        $event->getData()->willReturn(array());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('preSubmit', array($event))
        ;
    }

    function it_adds_a_value_form_field_with_correct_type_based_on_the_attribute_id(
        $attributeRepository,
        $formFactory,
        AttributeInterface $attribute,
        Form $form,
        Form $valueField,
        FormEvent $event
    ) {
        $event->getData()->willReturn(array(
            'attribute' => 6,
            'value' => array(
                'year'  => 2010,
                'month' => 01,
                'day'   => 01,
            ),
        ));
        $event->getForm()->willReturn($form);

        $attributeRepository->find(6)->willReturn($attribute);
        $attribute->getName()->willReturn('Release Date');
        $attribute->getType()->willReturn(DateAttributeType::TYPE);
        $attribute->getStorageType()->willReturn(AttributeValueInterface::STORAGE_DATE);

        $formFactory->createNamed('value', 'sylius_attribute_type_date', null, Argument::type('array'))->willReturn($valueField);
        $form->add($valueField)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
