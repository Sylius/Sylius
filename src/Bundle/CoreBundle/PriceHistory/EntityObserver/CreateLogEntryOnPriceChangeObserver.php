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

namespace Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver;

use Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Webmozart\Assert\Assert;

final class CreateLogEntryOnPriceChangeObserver implements EntityObserverInterface
{
    public function __construct(private PriceChangeLoggerInterface $priceChangeLogger)
    {
    }

    public function onChange(object $entity): void
    {
        Assert::isInstanceOf($entity, ChannelPricingInterface::class);

        $this->priceChangeLogger->log($entity);
    }

    public function supports(object $entity): bool
    {
        return $entity instanceof ChannelPricingInterface && null !== $entity->getPrice();
    }

    public function observedFields(): array
    {
        return ['price', 'originalPrice'];
    }
}
