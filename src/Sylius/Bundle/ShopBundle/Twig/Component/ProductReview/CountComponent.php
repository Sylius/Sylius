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

namespace Sylius\Bundle\ShopBundle\Twig\Component\ProductReview;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class CountComponent
{
    use HookableComponentTrait;

    public ProductInterface $product;

    /**
     * @param ProductReviewRepositoryInterface<ProductReview> $productReviewRepository
     */
    public function __construct(protected readonly ProductReviewRepositoryInterface $productReviewRepository)
    {
    }

    #[ExposeInTemplate('reviews_count')]
    public function countReviews(): int
    {
        return $this->productReviewRepository->countAcceptedByProduct($this->product);
    }
}
