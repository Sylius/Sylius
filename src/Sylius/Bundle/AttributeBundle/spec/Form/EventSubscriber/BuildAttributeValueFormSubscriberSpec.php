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
use Sylius\Bundle\AttributeBundle\Form\EventSubscriber\BuildAttributeValueFormSubscriber;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration\AttributeValueTypeConfigurationInterface;
use Sylius\Bundle\AttributeBundle\Factory\AttributeValueTypeConfigurationFactoryInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @mixin BuildAttributeValueFormSubscriber
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BuildAttributeValueFormSubscriberSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $attributeRepository,
        AttributeValueTypeConfigurationFactoryInterface $attributeValueTypeConfigurationFactory
    ) {
        $this->beConstructedWith($attributeRepository, 'server', $attributeValueTypeConfigurationFactory);
    }

    function it_is_initialized()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\EventSubscriber\BuildAttributeValueFormSubscriber');
    }

    function it_subscribes_to_pre_set_data_event()
    {
        self::getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData', FormEvents::PRE_SUBMIT => 'preSubmit']);
    }

    function it_does_not_add_any_field_when_attribute_is_new_or_empty(FormEvent $event, Form $form)
    {
        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($form);

        $form->add(Argument::any())->shouldNotBeCalled();
    }

    function it_adds_a_value_form_field_with_correct_type_based_on_the_attribute(
        $attributeValueTypeConfigurationFactory,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue,
        Form $form,
        FormEvent $event,
        AttributeValueTypeConfigurationInterface $configuration
    ) {
        $event->getData()->willReturn($attributeValue);
        $event->getForm()->willReturn($form);

        $attributeValue->getAttribute()->willReturn($attribute);
        $attribute->getName()->willReturn('Is promoted?');

        $attributeValueTypeConfigurationFactory->create($attribute, 'server', null)->willReturn($configuration);
        $configuration->getName()->willReturn('value');
        $configuration->getType()->willReturn('sylius_attribute_type_checkbox');
        $configuration->getFormOptions()->willReturn(['label' => $attribute->getName()]);

        $form->add('value', 'sylius_attribute_type_checkbox', Argument::type('array'))->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_throws_an_exception_on_pre_submit_event_when_attribute_id_is_undefined(FormEvent $event)
    {
        $event->getData()->willReturn([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('preSubmit', [$event])
        ;
    }

    function it_adds_a_value_form_field_with_correct_type_based_on_the_attribute_id(
        $attributeValueTypeConfigurationFactory,
        $attributeRepository,
        AttributeInterface $attribute,
        Form $form,
        FormEvent $event,
        AttributeValueTypeConfigurationInterface $configuration
    ) {
        $event->getData()->willReturn([
            'attribute' => 6,
            'value' => [
                'year' => 2010,
                'month' => 01,
                'day' => 01,
            ],
        ]);
        $event->getForm()->willReturn($form);

        $attributeRepository->find(6)->willReturn($attribute);
        $attribute->getName()->willReturn('Release Date');

        $attributeValueTypeConfigurationFactory->create($attribute, 'server', null)->willReturn($configuration);
        $configuration->getName()->willReturn('value');
        $configuration->getType()->willReturn('sylius_attribute_type_date');
        $configuration->getFormOptions()->willReturn(['label' => $attribute->getName()]);

        $form->add('value', 'sylius_attribute_type_date', Argument::type('array'))->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_adds_a_value_translation_form_field(
        $attributeValueTypeConfigurationFactory,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue,
        Form $form,
        FormEvent $event,
        AttributeValueTypeConfigurationInterface $configuration
    ) {
        $event->getData()->willReturn($attributeValue);
        $event->getForm()->willReturn($form);

        $attributeValue->getAttribute()->willReturn($attribute);
        $attribute->getName()->willReturn('Material');

        $attributeValueTypeConfigurationFactory->create($attribute, 'server', null)->willReturn($configuration);
        $configuration->getName()->willReturn('translations');
        $configuration->getType()->willReturn('a2lix_translationsForms');
        $configuration->getFormOptions()->willReturn([
            'form_type' => 'sylius_server_attribute_value_translation',
            'label' => $attribute->getName(),
            'form_options' => [
                'attr' => [
                    'data-name' => 'sylius_server[attributes][0][translations]',
                ],
                'value_translation_type' => 'sylius_attribute_type_text',
            ],
        ]);

        $form->add('translations', 'a2lix_translationsForms', Argument::type('array'))->shouldBeCalled();

        $this->preSetData($event);
    }
}
