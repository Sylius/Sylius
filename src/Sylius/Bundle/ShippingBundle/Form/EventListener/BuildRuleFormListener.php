<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\EventListener;

use Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
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
    /**
     * @var RuleCheckerRegistryInterface
     */
    private $checkerRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param RuleCheckerRegistryInterface $checkerRegistry
     * @param FormFactoryInterface         $factory
     */
    public function __construct(RuleCheckerRegistryInterface $checkerRegistry, FormFactoryInterface $factory)
    {
        $this->checkerRegistry = $checkerRegistry;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preBind'
        );
    }

    /**
     * Add the checker configuration if any.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $rule = $event->getData();

        if (null === $rule || null === $rule->getId()) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $rule->getType(), $rule->getConfiguration());
    }

    /**
     * Add the checker configuration if any.
     *
     * @param FormEvent $event
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('type', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['type']);
    }

    /**
     * Add the checker configuration fields.
     *
     * @param FormInterface $form
     * @param string        $ruleType
     * @param array         $data
     */
    protected function addConfigurationFields(FormInterface $form, $ruleType, array $data = array())
    {
        $checker = $this->checkerRegistry->getChecker($ruleType);
        $configurationField = $this->factory->createNamed('configuration', $checker->getConfigurationFormType(), $data, array('auto_initialize' => false));

        $form->add($configurationField);
    }
}
