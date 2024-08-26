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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class CountReviewsComponent
{
    public Product $product;

    /**
     * @param ProductReviewRepositoryInterface<ProductReview> $productReviewRepository
     */
    public function __construct(private readonly ProductReviewRepositoryInterface $productReviewRepository)
    {
    }

    #[ExposeInTemplate('reviews_count')]
    public function countReviews(): int
    {
        return $this->productReviewRepository->countAcceptedByProduct($this->product);
    }
}
