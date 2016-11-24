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

use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildPromotionActionFormSubscriber;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildChannelAwarePromotionActionFormSubscriber extends BuildPromotionActionFormSubscriber
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @inheritDoc
     */
    public function __construct(
        ServiceRegistryInterface $actionRegistry,
        FormFactoryInterface $factory,
        $registryIdentifier,
        ChannelRepositoryInterface $channelRepository
    ) {
        parent::__construct($actionRegistry, $factory, $registryIdentifier);

        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function addConfigurationFields(FormInterface $form, $registryIdentifier, array $data = [])
    {
        $model = $this->registry->get($registryIdentifier);

        $configurationCollection = $this->factory->createNamed('configuration', FormType::class, [], [
            'compound' => true,
            'auto_initialize' => false,
        ]);

        // temporary solution to prevent errors while rendering unexisting rule configuration type
        if (null === $configuration = $model->getConfigurationFormType()) {
            return;
        }

        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            $configurationField = $this->factory->createNamed(
                $channel->getCode(),
                $configuration,
                $data,
                [
                    'auto_initialize' => false,
                    'label' => $channel->getName(),
                ]
            );

            $configurationCollection->add($configurationField);
        }

        $form->add($configurationCollection);
    }
}
