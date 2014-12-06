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

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Promotion\Model\Action;
use Sylius\Component\Promotion\Model\ActionInterface;
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
     * @var ServiceRegistryInterface
     */
    private $actionRegistry;
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var string
     */
    private $actionType;

    public function __construct(ServiceRegistryInterface $actionRegistry, FormFactoryInterface $factory, $actionType = null)
    {
        $this->actionRegistry = $actionRegistry;
        $this->factory = $factory;
        $this->actionType = $actionType;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA  => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT    => 'preBind',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var ActionInterface $action */
        $action = $event->getData();

        if (null === $type = $this->getActionType($action)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $type, $this->getActionConfiguration($action));
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        /** @var ActionInterface $action */
        $action = $event->getData();

        if (null === $type = $this->getActionType($action)) {
            return;
        }

        $event->getForm()->get('type')->setData($type);
    }

    /**
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
     * @param FormInterface $form
     * @param string $actionType
     * @param array $data
     */
    protected function addConfigurationFields(FormInterface $form, $actionType, array $data = array())
    {
        $action = $this->actionRegistry->get($actionType);

        $configurationField = $this->factory->createNamed(
            'configuration',
            $action->getConfigurationFormType(),
            $data,
            array(
                'auto_initialize' => false,
                'label' => false,
            )
        );

        $form->add($configurationField);
    }

    /**
     * Get action configuration
     *
     * @param ActionInterface $action
     *
     * @return array
     */
    protected function getActionConfiguration($action)
    {
        if ($action instanceof ActionInterface && null !== $action->getConfiguration()) {
            return $action->getConfiguration();
        }

        return array();
    }

    /**
     * Get action type
     *
     * @param ActionInterface $action
     *
     * @return null|string
     */
    protected function getActionType($action)
    {
        if ($action instanceof ActionInterface && null !== $action->getType()) {
            return $action->getType();
        }

        if (null !== $this->actionType) {
            return $this->actionType;
        }

        return null;
    }
}
