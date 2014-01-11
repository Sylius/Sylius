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

use Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds configuration form to a action,
 * if selected action requires one.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class BuildActionFormListener implements EventSubscriberInterface
{
    /**
     * @var PromotionActionRegistryInterface
     */
    private $actionRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    public function __construct(PromotionActionRegistryInterface $actionRegistry, FormFactoryInterface $factory)
    {
        $this->actionRegistry = $actionRegistry;
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preBind'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $action = $event->getData();

        if (null === $action || null === $action->getId()) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $action->getType(), $action->getConfiguration());
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('type', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['type']);
    }

    protected function addConfigurationFields(FormInterface $form, $actionType, array $data = array())
    {
        $action = $this->actionRegistry->getAction($actionType);
        $configurationField = $this->factory->createNamed('configuration', $action->getConfigurationFormType(), $data, array('auto_initialize' => false));

        $form->add($configurationField);
    }
}
