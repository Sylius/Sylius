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
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;

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

        $this->beConstructedWith($checkerRegistry, $factory);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Form\EventListener\BuildRuleFormListener');
    }

    function it_should_be_event_subscripber()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    /**
     * @param \Symfony\Component\Form\FormEvent                   $event
     * @param \Sylius\Bundle\PromotionsBundle\Model\RuleInterface $rule
     * @param \Symfony\Component\Form\Form                        $form
     * @param \Symfony\Component\Form\Form                        $field
     */
    function it_should_add_configuration_fields_in_pre_set_data($checkerRegistry, $factory, $event, $rule, $form, $field)
    {
        $event->getData()->shouldBeCalled()->willReturn($rule);
        $event->getForm()->shouldBeCalled()->willReturn($form);
        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn(array());

        $factory->createNamed('configuration', 'sylius_promotion_rule_item_total_configuration', Argument::cetera())->shouldBeCalled()->willReturn($field);
        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }
}
