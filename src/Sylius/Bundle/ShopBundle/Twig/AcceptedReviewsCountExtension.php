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

namespace Sylius\Bundle\ShopBundle\Twig;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AcceptedReviewsCountExtension extends AbstractExtension
{
    /**
     * @param ProductReviewRepositoryInterface<ProductReview> $productReviewRepository
     */
    public function __construct(
        private ProductReviewRepositoryInterface $productReviewRepository,
        private LocaleContextInterface $localeContext,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_count_product_reviews', [$this, 'countProductReviews']),
        ];
    }

    public function countProductReviews(ProductInterface $product): int
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return count($this->productReviewRepository->findAcceptedByProductSlugAndChannel($product->getSlug(), $this->localeContext->getLocaleCode(), $channel));
    }
}
