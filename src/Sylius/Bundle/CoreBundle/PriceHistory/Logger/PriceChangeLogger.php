<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PriceHistory\Logger;

use Doctrine\Persistence\ObjectManager;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Factory\ChannelPricingLogEntryFactoryInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Webmozart\Assert\Assert;

final class PriceChangeLogger implements PriceChangeLoggerInterface
{
    public function __construct(
        private ChannelPricingLogEntryFactoryInterface $logEntryFactory,
        private ObjectManager $logEntryManager,
        private DateTimeProviderInterface $dateTimeProvider,
    ) {
    }

    public function log(ChannelPricingInterface $channelPricing): void
    {
        Assert::notNull($channelPricing->getPrice());

        $logEntry = $this->logEntryFactory->create(
            $channelPricing,
            $this->dateTimeProvider->now(),
            $channelPricing->getPrice(),
            $channelPricing->getOriginalPrice(),
        );

        $this->logEntryManager->persist($logEntry);
    }
}
