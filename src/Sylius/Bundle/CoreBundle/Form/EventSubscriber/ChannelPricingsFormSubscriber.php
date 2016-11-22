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

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelPricingsFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var FactoryInterface
     */
    private $channelPricingFactory;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param FactoryInterface $channelPricingFactory
     */
    public function __construct(ChannelRepositoryInterface $channelRepository, FactoryInterface $channelPricingFactory)
    {
        $this->channelRepository = $channelRepository;
        $this->channelPricingFactory = $channelPricingFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $event->getData();

        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            if ($productVariant->hasChannelPricingForChannel($channel)) {
                continue;
            }

            /** @var ChannelPricingInterface $channelPricing */
            $channelPricing = $this->channelPricingFactory->createNew();
            $channelPricing->setChannel($channel);
            $productVariant->addChannelPricing($channelPricing);
        }
    }
}
