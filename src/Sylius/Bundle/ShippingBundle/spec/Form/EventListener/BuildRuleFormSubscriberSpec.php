<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Checker\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\Rule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class BuildRuleFormSubscriberSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $checkerRegistry, FormFactoryInterface $factory)
    {
        $this->beConstructedWith($checkerRegistry, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\EventListener\BuildRuleFormSubscriber');
    }

    function it_is_a_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ]);
    }

    function it_adds_configuration_field_on_pre_set_data(
        $checkerRegistry,
        $factory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $formConfiguration,
        Rule $rule,
        RuleCheckerInterface $checker
    ) {
        $event->getData()->shouldBeCalled()->willReturn($rule);
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $rule->getId()->shouldBeCalled()->willreturn(12);
        $rule->getType()->shouldBeCalled()->willreturn('rule_type');
        $rule->getConfiguration()->shouldBeCalled()->willreturn([]);

        $checkerRegistry->get('rule_type')->shouldBeCalled()->willreturn($checker);
        $checker->getConfigurationFormType()->shouldBeCalled()->willreturn('configuration_form_type');

        $factory->createNamed(
            'configuration',
            'configuration_form_type',
            [],
            ['auto_initialize' => false]
        )->shouldBeCalled()->willreturn($formConfiguration);

        $form->add($formConfiguration)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_configuration_field_on_post_submit(
        $checkerRegistry,
        $factory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $formConfiguration,
        RuleCheckerInterface $checker
    ) {
        $event->getData()->shouldBeCalled()->willReturn(['type' => 'rule_type']);
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $checkerRegistry->get('rule_type')->shouldBeCalled()->willreturn($checker);
        $checker->getConfigurationFormType()->shouldBeCalled()->willreturn('configuration_form_type');

        $factory->createNamed(
            'configuration',
            'configuration_form_type',
            [],
            ['auto_initialize' => false]
        )->shouldBeCalled()->willreturn($formConfiguration);

        $form->add($formConfiguration)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
