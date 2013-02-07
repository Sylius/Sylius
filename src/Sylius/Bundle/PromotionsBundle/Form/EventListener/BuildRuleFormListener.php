<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Form\EventListener;

use Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds configuration form to a rule,
 * if selected rule requires one.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class BuildRuleFormListener implements EventSubscriberInterface
{
    private $checkerRegistry;
    private $factory;

    public function __construct(RuleCheckerRegistryInterface $checkerRegistry, FormFactoryInterface $factory)
    {
        $this->checkerRegistry = $checkerRegistry;
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    public function preSetData(DataEvent $event)
    {
        $rule = $event->getData();
        $form = $event->getForm();

        if (null === $rule || null === $rule->getId()) {
            return;
        }

        $this->addConfigurationFields($form, $rule->getType(), $rule->getConfiguration());
    }

    protected function addConfigurationFields(FormInterface $form, $ruleType, array $data = array())
    {
        $checker = $this->checkerRegistry->getChecker($ruleType);

        if (true !== $checker->isConfigurable()) {
            return;
        }

        $configurationField = $this->factory->createNamed('configuration', $checker->getConfigurationFormType(), $data);

        $form->add($configurationField);
    }
}
