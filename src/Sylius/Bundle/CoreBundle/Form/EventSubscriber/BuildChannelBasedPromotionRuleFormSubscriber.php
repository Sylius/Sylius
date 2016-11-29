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
use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildPromotionRuleFormSubscriber;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ChannelBasedRuleCheckerInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class BuildChannelBasedPromotionRuleFormSubscriber extends BuildPromotionRuleFormSubscriber
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param ServiceRegistryInterface $ruleCheckerRegistry
     * @param FormFactoryInterface $factory
     * @param string $registryIdentifier
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        ServiceRegistryInterface $ruleCheckerRegistry,
        FormFactoryInterface $factory,
        $registryIdentifier,
        ChannelRepositoryInterface $channelRepository
    ) {
        parent::__construct($ruleCheckerRegistry, $factory, $registryIdentifier);

        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function addConfigurationFields(FormInterface $form, $registryIdentifier, array $data = [])
    {
        $model = $this->registry->get($registryIdentifier);

        $configuration = $model->getConfigurationFormType();
        if (null === $configuration) {
            return;
        }

        if (!$model instanceof ChannelBasedRuleCheckerInterface) {
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
