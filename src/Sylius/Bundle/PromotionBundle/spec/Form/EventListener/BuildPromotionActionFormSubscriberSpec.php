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
use Sylius\Bundle\PromotionBundle\Form\EventListener\AbstractConfigurationSubscriber;
use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildPromotionActionFormSubscriber;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @mixin BuildPromotionActionFormSubscriber
 *
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
final class BuildPromotionActionFormSubscriberSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $registry,
        PromotionActionCommandInterface $action,
        FormFactoryInterface $factory
    ) {
        $action->getConfigurationFormType()->willReturn('sylius_promotion_action_fixed_discount_configuration');
        $registry->get('test_action')->willReturn($action);

        $this->beConstructedWith($registry, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BuildPromotionActionFormSubscriber::class);
    }

    function it_is_configuration_subscriber()
    {
        $this->shouldImplement(AbstractConfigurationSubscriber::class);
    }

    function it_subscribes_to_events()
    {
        static::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ]);
    }

    function it_adds_configuration_fields_in_pre_set_data(
        FormFactoryInterface $factory,
        PromotionActionInterface $action,
        FormEvent $event,
        Form $form,
        Form $field
    ) {
        $event->getData()->willReturn($action);
        $event->getForm()->willReturn($form);
        $action->getType()->willReturn('test_action');
        $action->getConfiguration()->willReturn([]);

        $factory->createNamed(
            'configuration',
            'sylius_promotion_action_fixed_discount_configuration',
            Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_configuration_fields_in_pre_submit_data(
        FormFactoryInterface $factory,
        FormEvent $event,
        Form $form,
        Form $field
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn(['type' => 'test_action']);

        $factory->createNamed(
            'configuration',
            'sylius_promotion_action_fixed_discount_configuration',
            Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_sets_type_in_post_set_data(
        PromotionActionInterface $action,
        FormEvent $event,
        Form $form
    ) {
        $event->getData()->willReturn($action);
        $event->getForm()->willReturn($form);
        $action->getType()->willReturn('test_action');

        $form->get('type')->willReturn($form);
        $form->setData('test_action')->shouldBeCalled();

        $this->postSetData($event);
    }
}
