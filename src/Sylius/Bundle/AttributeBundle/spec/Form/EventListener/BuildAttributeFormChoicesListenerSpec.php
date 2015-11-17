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
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeFormChoicesListenerSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $formFactory, ServiceRegistryInterface $attributeTypeRegistry)
    {
        $this->beConstructedWith($formFactory, $attributeTypeRegistry);
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_to_pre_set_data_and_pre_submit_events()
    {
        self::getSubscribedEvents()->shouldReturn(array(
            'form.pre_set_data' => 'setAttributeStorageType',
        ));
    }

    function it_sets_attribute_storage_type_based_on_chosen_attribute_type(
        $attributeTypeRegistry,
        AttributeInterface $attribute,
        AttributeTypeInterface $attributeType,
        FormEvent $event
    ) {
        $event->getData()->willReturn($attribute);

        $attribute->getType()->willReturn('text');
        $attributeTypeRegistry->get('text')->willReturn($attributeType);
        $attributeType->getStorageType()->willReturn('text');

        $attribute->setStorageType('text')->shouldBeCalled();

        $this->setAttributeStorageType($event);
    }
}
