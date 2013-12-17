<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;
use Sylius\Bundle\PromotionsBundle\Model\Rule;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class BuildRuleFormListenerSpec extends ObjectBehavior
{
    /**
     * @param \Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface $checkerRegistry
     * @param \Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface                  $checker
     * @param \Symfony\Component\Form\FormFactoryInterface                                  $factory
     */
    function let($checkerRegistry, $checker, $factory)
    {
        $checker->getConfigurationFormType()->willReturn('sylius_promotion_rule_item_total_configuration');
        $checkerRegistry->getChecker(Argument::any())->willReturn($checker);

        $this->beConstructedWith($checkerRegistry, $factory, RuleInterface::TYPE_ITEM_TOTAL);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Form\EventListener\BuildRuleFormListener');
    }

    function it_should_be_event_subscripber()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_should_add_configuration_fields_in_pre_set_data(
        FormEvent $event,
        Form $form,
        Rule $rule,
        RuleCheckerRegistryInterface $checkerRegistry,
        RuleCheckerInterface $checker,
        FormFactoryInterface $factory,
        Form $field)
    {
        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->willReturn(array());

        $event->getData()->shouldBeCalled()->willReturn($rule);
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $checker->getConfigurationFormType()
            ->shouldBeCalled()
            ->willReturn('sylius_promotion_rule_item_total_configuration');

        $checkerRegistry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)
            ->shouldBeCalled()
            ->willReturn($checker);

        $factory->createNamed('configuration', 'sylius_promotion_rule_item_total_configuration', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_should_add_automatically_configuration_fields_in_pre_set_data(
        FormEvent $event,
        Form $form,
        RuleCheckerRegistryInterface $checkerRegistry,
        RuleCheckerInterface $checker,
        FormFactoryInterface $factory,
        Form $field)
    {
        $event->getData()->shouldBeCalled();
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $checker->getConfigurationFormType()
            ->shouldBeCalled()
            ->willReturn('sylius_promotion_rule_item_total_configuration');

        $checkerRegistry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)
            ->shouldBeCalled()
            ->willReturn($checker);

        $factory->createNamed('configuration', 'sylius_promotion_rule_item_total_configuration', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }
}
