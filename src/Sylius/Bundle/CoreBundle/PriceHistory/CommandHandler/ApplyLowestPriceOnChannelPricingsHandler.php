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

namespace Sylius\Bundle\CoreBundle\PriceHistory\CommandHandler;

use Sylius\Bundle\CoreBundle\PriceHistory\Command\ApplyLowestPriceOnChannelPricings;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ApplyLowestPriceOnChannelPricingsHandler
{
    public function __construct(
        private ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        private RepositoryInterface $channelPricingRepository,
    ) {
    }

    public function __invoke(ApplyLowestPriceOnChannelPricings $applyLowestPriceOnChannelPricings): void
    {
        /** @var ChannelPricingInterface[] $channelPricings */
        $channelPricings = $this->channelPricingRepository->findBy(
            ['id' => $applyLowestPriceOnChannelPricings->channelPricingIds],
        );

        foreach ($channelPricings as $channelPricing) {
            $this->productLowestPriceBeforeDiscountProcessor->process($channelPricing);
        }
    }
}
