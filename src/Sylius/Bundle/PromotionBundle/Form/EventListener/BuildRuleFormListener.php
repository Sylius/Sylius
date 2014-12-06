<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\EventListener;

use Sylius\Component\Promotion\Model\Rule;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 *
 * This listener adds configuration form to a rule,
 * if selected rule requires one.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class BuildRuleFormListener implements EventSubscriberInterface
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
     * @var string
     */
    private $ruleType;

    public function __construct(ServiceRegistryInterface $checkerRegistry, FormFactoryInterface $factory, $type = null)
    {
        $this->checkerRegistry = $checkerRegistry;
        $this->factory = $factory;
        $this->ruleType = $type;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA  => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT    => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var Rule $rule */
        $rule = $event->getData();

        if (null === $type = $this->getRuleType($rule)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $type, $this->getRuleConfiguration($rule));
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        /** @var RuleInterface $rule */
        $rule = $event->getData();

        if (null === $type = $this->getRuleType($rule)) {
            return;
        }

        $event->getForm()->get('type')->setData($type);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        /** @var RuleInterface $rule */
        $data = $event->getData();

        if (empty($data) || !array_key_exists('type', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['type']);
    }

    /**
     * @param FormInterface $form
     * @param string $ruleType
     * @param array $data
     */
    protected function addConfigurationFields(FormInterface $form, $ruleType, array $data = array())
    {
        $checker = $this->checkerRegistry->get($ruleType);

        $configurationField = $this->factory->createNamed(
            'configuration',
            $checker->getConfigurationFormType(),
            $data,
            array(
                'auto_initialize' => false,
                'label' => false,
            )
        );

        $form->add($configurationField);
    }

    /**
     * Get Rule configuration
     *
     * @param RuleInterface $rule
     *
     * @return array
     */
    protected function getRuleConfiguration($rule)
    {
        if ($rule instanceof RuleInterface && null !== $rule->getConfiguration()) {
            return $rule->getConfiguration();
        }

        return array();
    }

    /**
     * Get rule type
     *
     * @param RuleInterface $rule
     *
     * @return null|string
     */
    protected function getRuleType($rule)
    {
        if ($rule instanceof RuleInterface && null !== $rule->getType()) {
            return $rule->getType();
        }

        if (null !== $this->ruleType) {
            return $this->ruleType;
        }

        return null;
    }
}