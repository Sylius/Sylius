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

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class BuildProductPropertyFormListenerSpec extends ObjectBehavior
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
        self::getSubscribedEvents()->shouldReturn(array('form.pre_set_data' => 'buildForm'));
    }

    /**
     * @param Symfony\Component\Form\FormEvent $event
     * @param Symfony\Component\Form\Form      $form
     * @param Symfony\Component\Form\Form      $valueField
     */
    function it_builds_form_with_property_and_value_when_new_product_property(
        $event, $form, $valueField, $formFactory
    )
    {
        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($form);

        $formFactory->createNamed('value', 'text', null, Argument::any())->willReturn($valueField)->shouldBeCalled();
        $form->add($valueField)->shouldBeCalled()->willReturn($form);

        $this->buildForm($event);
    }

    /**
     * @param Symfony\Component\Form\FormEvent                           $event
     * @param Symfony\Component\Form\Form                                $form
     * @param Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface $productProperty
     * @param Symfony\Component\Form\Form                                $valueField
     */
    function it_builds_value_field_base_on_product_property(
        $event, $form, $productProperty, $valueField, $formFactory
    )
    {
        $productProperty->getType()->willReturn('checkbox');
        $productProperty->getName()->willReturn('My name');
        $productProperty->getConfiguration()->willReturn(array());

        $event->getData()->willReturn($productProperty);
        $event->getForm()->willReturn($form);

        $formFactory->createNamed('value', 'checkbox', null, array('label' => 'My name', 'auto_initialize' => false))->willReturn($valueField)->shouldBeCalled();

        $form->remove('property')->shouldBeCalled()->willReturn($form);
        $form->add($valueField)->shouldBeCalled()->willReturn($form);

        $this->buildForm($event);
    }

    /**
     * @param Symfony\Component\Form\FormEvent                           $event
     * @param Symfony\Component\Form\Form                                $form
     * @param Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface $productProperty
     * @param Symfony\Component\Form\Form                                $valueField
     */
    function it_builds_options_base_on_product_property(
        $event, $form, $productProperty, $valueField, $formFactory
    )
    {
        $productProperty->getType()->willReturn('choice');
        $productProperty->getConfiguration()->willReturn(array(
            'choices' => array(
                'red'  => 'Red',
                'blue' => 'Blue'
            )
        ));
        $productProperty->getName()->willReturn('My name');

        $event->getData()->willReturn($productProperty);
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

        $form->remove('property')->shouldBeCalled()->willReturn($form);
        $form->add($valueField)->shouldBeCalled()->willReturn($form);

        $this->buildForm($event);
    }
}
