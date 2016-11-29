<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Sylius\Bundle\CoreBundle\Form\Type\Promotion\PromotionConfigurationType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Promotion\Action\ChannelBasedPromotionActionCommandInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class BuildChannelBasedPromotionFormSubscriber
{
    /**
     * @var ServiceRegistryInterface
     */
    private $registry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var string
     */
    private $subject;

    /**
     * @param ServiceRegistryInterface $registry
     * @param FormFactoryInterface $factory
     * @param ChannelRepositoryInterface $channelRepository
     * @param string $subject
     */
    public function __construct(
        ServiceRegistryInterface $registry,
        FormFactoryInterface $factory,
        ChannelRepositoryInterface $channelRepository,
        $subject
    ) {
        $this->registry = $registry;
        $this->factory = $factory;
        $this->channelRepository = $channelRepository;
        $this->subject = $subject;
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
        $action = $event->getData();

        if (null === $type = $this->getRegistryIdentifier($action, $event->getForm())) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $type, $this->getConfiguration($action));
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $action = $event->getData();

        if (null === $type = $this->getRegistryIdentifier($action, $event->getForm())) {
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
     * @param mixed $dynamicType
     * @param FormInterface $form
     *
     * @return null|string
     */
    private function getRegistryIdentifier($dynamicType, FormInterface $form)
    {
        if ($dynamicType instanceof $this->subject && null !== $dynamicType->getType()) {
            return $dynamicType->getType();
        }

        if (null !== $form->getConfig()->hasOption('configuration_type')) {
            return $form->getConfig()->getOption('configuration_type');
        }

        return null;
    }

    /**
     * @param mixed $dynamicType
     *
     * @return array
     */
    private function getConfiguration($dynamicType)
    {
        if ($dynamicType instanceof $this->subject && null !== $dynamicType->getConfiguration()) {
            return $dynamicType->getConfiguration();
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    private function addConfigurationFields(FormInterface $form, $registryIdentifier, array $data = [])
    {
        $model = $this->registry->get($registryIdentifier);

        $configuration = $model->getConfigurationFormType();
        if (null === $configuration) {
            return;
        }

        if (!$model instanceof ChannelBasedPromotionActionCommandInterface) {
            $form->add($this->createConfigurationField($configuration, $data));

            return;
        }

        $configurationCollection = $this->factory->createNamed('configuration', PromotionConfigurationType::class, [], [
            'compound' => true,
            'auto_initialize' => false,
        ]);

        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            $configurationCollection->add($this->createConfigurationFieldForChannel($channel, $configuration, $data));
        }

        $form->add($configurationCollection);
    }

    /**
     * @param ChannelInterface $channel
     * @param string $configuration
     * @param array $data
     *
     * @return FormInterface
     */
    private function createConfigurationFieldForChannel(
        ChannelInterface $channel,
        $configuration,
        array $data
    ) {
        $config = [
            'auto_initialize' => false,
            'label' => $channel->getName(),
            'currency' => $channel->getBaseCurrency()->getCode(),
        ];

        $data = empty($data) ? $data : $data[$channel->getCode()];

        return $this->factory->createNamed($channel->getCode(), $configuration, $data, $config);
    }

    /**
     * @param string $configuration
     * @param array $data
     *
     * @return FormInterface
     */
    private function createConfigurationField($configuration, array $data)
    {
        return $this->factory->createNamed('configuration', $configuration, $data, [
            'auto_initialize' => false,
            'label' => false,
        ]);
    }
}
