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
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class BuildRuleFormSubscriberSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $registry,
        RuleCheckerInterface $checker,
        FormFactoryInterface $factory
    ) {
        $checker->getConfigurationFormType()->willReturn('sylius_promotion_rule_item_total_configuration');
        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);

        $this->beConstructedWith($registry, $factory);
    }

    function it_is_initializabled()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\EventListener\BuildRuleFormSubscriber');
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
        RuleInterface $rule,
        Form $form,
        Form $field
    ) {
        $event->getData()->willReturn($rule);
        $event->getForm()->willReturn($form);

        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->willReturn(array());

        $factory->createNamed('configuration', 'sylius_promotion_rule_item_total_configuration', Argument::cetera())->shouldBeCalled()->willReturn($field);
        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_configuration_fields_in_pre_submit_data(
        $factory,
        FormEvent $event,
        RuleInterface $rule,
        Form $form,
        Form $field
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn(array('type' =>RuleInterface::TYPE_ITEM_TOTAL));


        $factory->createNamed('configuration', 'sylius_promotion_rule_item_total_configuration', Argument::cetera())->shouldBeCalled()->willReturn($field);
        $form->add($field)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_sets_type_in_post_set_data(
        FormEvent $event,
        RuleInterface $rule,
        Form $form
    ) {
        $event->getData()->willReturn($rule);
        $event->getForm()->willReturn($form);
        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);

        $form->get('type')->willReturn($form);
        $form->setData(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled();

        $this->postSetData($event);
    }
}
