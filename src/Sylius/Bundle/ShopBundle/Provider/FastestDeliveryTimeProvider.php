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

namespace Sylius\Bundle\ShopBundle\Provider;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

final class FastestDeliveryTimeProvider
{
    public function __construct(
        private readonly ShippingMethodRepositoryInterface $shippingMethodRepository,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    /**
     * @return array{minimumDays:int, maximumDays:int}|null
     */
    public function findBestRange(): ?array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        /** @var iterable<ShippingMethodInterface> $methods */
        $methods = $this->shippingMethodRepository->findEnabledForChannel($channel);

        $best = null;

        foreach ($methods as $method) {
            $range = $this->normalizeDeliveryRange($method->getMinDeliveryTimeDays(), $method->getMaxDeliveryTimeDays());
            if ($range === null) {
                continue;
            }

            [$minimumDays, $maximumDays] = $range;

            if (
                $best === null ||
                $maximumDays < $best['maximumDays'] ||
                ($maximumDays === $best['maximumDays'] && $minimumDays < $best['minimumDays'])
            ) {
                $best = ['minimumDays' => $minimumDays, 'maximumDays' => $maximumDays];
            }
        }

        return $best;
    }

    /**
     * @return array{0:int,1:int}|null
     */
    private function normalizeDeliveryRange(?int $minDays, ?int $maxDays): ?array
    {
        if ($minDays === null && $maxDays === null) {
            return null;
        }

        if ($minDays !== null && $maxDays !== null) {
            return [min($minDays, $maxDays), max($minDays, $maxDays)];
        }

        $value = $minDays ?? $maxDays ?? 0;

        return [$value, $value];
    }
}
