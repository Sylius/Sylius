<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class BuildActionFormSubscriberSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $registry,
        PromotionActionInterface $action,
        FormFactoryInterface $factory
    ) {
        $action->getConfigurationFormType()->willReturn('sylius_promotion_action_fixed_discount_configuration');
        $registry->get(ActionInterface::TYPE_FIXED_DISCOUNT)->willReturn($action);

        $this->beConstructedWith($registry, $factory);
    }

    function it_is_initializabled()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\EventListener\BuildActionFormSubscriber');
    }

    function it_is_configuration_subscriber()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionBundle\Form\EventListener\AbstractConfigurationSubscriber');
    }

    function it_subscribes_evetns()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ));
    }

    function it_adds_configuration_fields_in_pre_set_data(
        $factory,
        FormEvent $event,
        ActionInterface $action,
        Form $form,
        Form $field
    ) {
        $event->getData()->willReturn($action);
        $event->getForm()->willReturn($form);
        $action->getType()->willReturn(ActionInterface::TYPE_FIXED_DISCOUNT);
        $action->getConfiguration()->willReturn(array());

        $factory->createNamed(
            'configuration',
            'sylius_promotion_action_fixed_discount_configuration',
            Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_configuration_fields_in_pre_submit_data(
        $factory,
        FormEvent $event,
        ActionInterface $action,
        Form $form,
        Form $field
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn(array('type' =>ActionInterface::TYPE_FIXED_DISCOUNT));


        $factory->createNamed(
            'configuration',
            'sylius_promotion_action_fixed_discount_configuration',
            Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_sets_type_in_post_set_data(
        FormEvent $event,
        ActionInterface $action,
        Form $form
    ) {
        $event->getData()->willReturn($action);
        $event->getForm()->willReturn($form);
        $action->getType()->willReturn(ActionInterface::TYPE_FIXED_DISCOUNT);

        $form->get('type')->willReturn($form);
        $form->setData(ActionInterface::TYPE_FIXED_DISCOUNT)->shouldBeCalled();

        $this->postSetData($event);
    }
}
