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

use Sylius\Component\Promotion\Model\PromotionDynamicTypeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractConfigurationSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @param ServiceRegistryInterface $registry
     * @param FormFactoryInterface $factory
     */
    public function __construct(
        ServiceRegistryInterface $registry,
        FormFactoryInterface $factory
    ) {
        $this->registry = $registry;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $type = $this->getRegistryIdentifier($data, $event->getForm())) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $type, $this->getConfiguration($data));
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $type = $this->getRegistryIdentifier($data, $event->getForm())) {
            return;
        }

        $event->getForm()->get('type')->setData($type);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('type', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['type']);
    }

    /**
     * @param FormInterface $form
     * @param string        $registryIdentifier
     * @param array         $data
     */
    protected function addConfigurationFields(FormInterface $form, $registryIdentifier, array $data = [])
    {
        // FIXME: Unknown type of $model, may crash in any moment!
        $model = $this->registry->get($registryIdentifier);

        // temporary solution to prevent errors while rendering unexisting configuration type
        if (null === $configuration = $model->getConfigurationFormType()) {
            return;
        }

        $configurationField = $this->factory->createNamed(
            'configuration',
            $configuration,
            $data,
            [
                'auto_initialize' => false,
                'label' => false,
            ]
        );

        $form->add($configurationField);
    }

    /**
     * @param PromotionDynamicTypeInterface|null $modelType
     * @param FormInterface $form
     *
     * @return null|string
     */
    protected function getRegistryIdentifier(PromotionDynamicTypeInterface $modelType = null, FormInterface $form)
    {
        if ($modelType instanceof PromotionDynamicTypeInterface && null !== $modelType->getType()) {
            return $modelType->getType();
        }

        if (null !== $form->getConfig()->hasOption('configuration_type')) {
            return $form->getConfig()->getOption('configuration_type');
        }

        return null;
    }

    /**
     * @return array
     */
    abstract protected function getConfiguration($model);
}
