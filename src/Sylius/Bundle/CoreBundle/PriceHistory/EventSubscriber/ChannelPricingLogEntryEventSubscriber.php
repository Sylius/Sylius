<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PriceHistory\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;

final class ChannelPricingLogEntryEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private ProductLowestPriceBeforeDiscountProcessorInterface $lowestPriceProcessor)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        /** @var ChannelPricingLogEntryInterface $entity */
        $entity = $event->getObject();

        if (!$entity instanceof ChannelPricingLogEntryInterface) {
            return;
        }

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $entity->getChannelPricing();

        $this->lowestPriceProcessor->process($channelPricing);
    }
}
