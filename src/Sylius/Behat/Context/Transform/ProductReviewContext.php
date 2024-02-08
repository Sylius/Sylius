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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Webmozart\Assert\Assert;

final class ProductReviewContext implements Context
{
    public function __construct(private RepositoryInterface $productReviewRepository)
    {
    }

    /**
     * @Transform :productReview
     * @Transform /^"([^"]+)" product review$/
     */
    public function getProductReviewByTitle(string $title): ReviewInterface
    {
        $productReview = $this->productReviewRepository->findOneBy(['title' => $title]);

        Assert::notNull(
            $productReview,
            sprintf('Product review with title "%s" does not exist', $title),
        );

        return $productReview;
    }
}
