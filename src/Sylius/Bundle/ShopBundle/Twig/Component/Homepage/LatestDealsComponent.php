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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Homepage;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class LatestDealsComponent
{
    public const DEFAULT_LIMIT = 4;

    public int $limit = self::DEFAULT_LIMIT;

    /** @param ProductRepositoryInterface<ProductInterface> $productRepository */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly LocaleContextInterface $localeContext,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    /**
     * @return array<ProductInterface>
     */
    #[ExposeInTemplate(name: 'latest_deals')]
    public function getLatestDeals(): array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $localeCode = $this->localeContext->getLocaleCode();

        return $this->productRepository->findLatestByChannel($channel, $localeCode, $this->limit);
    }
}
