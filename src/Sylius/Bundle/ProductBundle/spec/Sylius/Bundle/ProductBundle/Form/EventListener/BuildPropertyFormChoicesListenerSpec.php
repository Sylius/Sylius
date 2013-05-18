<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Model\PropertyTypes;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class BuildPropertyFormChoicesListenerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    function let($formFactory)
    {
        $this->beConstructedWith($formFactory);
    }

    function it_subscribes_to_pre_set_data_event()
    {
        self::getSubscribedEvents()->shouldReturn(array('form.pre_set_data' => 'buildChoices'));
    }

    /**
     * @param Symfony\Component\Form\FormEvent $event
     * @param Symfony\Component\Form\Form      $form
     */
    function it_does_no_not_build_choices_collection_for_null(
        $event, $form, $formFactory
    )
    {
        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($form);

        $formFactory
            ->createNamed(Argument::any())
            ->shouldNotBeCalled()
        ;
        $form->add(Argument::any())->shouldNotBeCalled();

        $this->buildChoices($event);
    }

    /**
     * @param Symfony\Component\Form\FormEvent                    $event
     * @param Symfony\Component\Form\Form                         $form
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     * @param Symfony\Component\Form\Form                         $collectionField
     */
    function it_builds_choices_collection_for_new_object_without_type(
        $event, $form, $property, $collectionField, $formFactory
    )
    {
        $event->getData()->willReturn($property);
        $event->getForm()->willReturn($form);

        $property->getType()->willReturn(null);

        $formFactory
            ->createNamed('choices', 'collection', null, array(
                'type'         => 'text',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->willReturn($collectionField)
            ->shouldBeCalled()
        ;
        $form->add($collectionField)->shouldBeCalled()->willReturn($form);

        $this->buildChoices($event);
    }

    /**
     * @param Symfony\Component\Form\FormEvent                    $event
     * @param Symfony\Component\Form\Form                         $form
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     * @param Symfony\Component\Form\Form                         $collectionField
     */
    function it_builds_choices_collection_for_choice_property(
        $event, $form, $property, $collectionField, $formFactory
    )
    {
        $event->getData()->willReturn($property);
        $event->getForm()->willReturn($form);

        $property->getType()->willReturn(PropertyTypes::CHOICE);

        $formFactory
            ->createNamed('choices', 'collection', null, array(
                'type'         => 'text',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->willReturn($collectionField)
            ->shouldBeCalled()
        ;
        $form->add($collectionField)->shouldBeCalled()->willReturn($form);

        $this->buildChoices($event);
    }

    /**
     * @param Symfony\Component\Form\FormEvent                    $event
     * @param Symfony\Component\Form\Form                         $form
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     * @param Symfony\Component\Form\Form                         $collectionField
     */
    function it_does_not_build_choices_collection_for_other_than_choice_property_types(
        $event, $form, $property, $collectionField, $formFactory
    )
    {
        $property->getType()->willReturn(PropertyTypes::TEXT);

        $event->getData()->willReturn($property);
        $event->getForm()->willReturn($form);

        $formFactory
            ->createNamed('choices', 'collection', null, Argument::any())
            ->willReturn($collectionField)
            ->shouldNotBeCalled()
        ;
        $form->add(Argument::any())->shouldNotBeCalled();

        $this->buildChoices($event);
    }
}
