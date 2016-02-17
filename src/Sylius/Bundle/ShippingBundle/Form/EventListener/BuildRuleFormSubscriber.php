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

use Sylius\Component\Registry\ServiceRegistryInterface;
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
class BuildRuleFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $checkerRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @param ServiceRegistryInterface $checkerRegistry
     * @param FormFactoryInterface     $factory
     */
    public function __construct(ServiceRegistryInterface $checkerRegistry, FormFactoryInterface $factory)
    {
        $this->checkerRegistry = $checkerRegistry;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $rule = $event->getData();
        $form = $event->getForm();

        if (null === $rule || null === $rule->getId()) {
            return;
        }

        $this->addConfigurationFields($form, $rule->getType(), $rule->getConfiguration());
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (empty($data) || !array_key_exists('type', $data)) {
            return;
        }

        $this->addConfigurationFields($form, $data['type']);
    }

    /**
     * @param FormInterface $form
     * @param string        $ruleType
     * @param array         $data
     */
    protected function addConfigurationFields(FormInterface $form, $ruleType, array $data = [])
    {
        $checker = $this->checkerRegistry->get($ruleType);
        $configurationField = $this->factory->createNamed('configuration', $checker->getConfigurationFormType(), $data, ['auto_initialize' => false]);

        $form->add($configurationField);
    }
}
