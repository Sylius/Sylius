<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductReviewContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $productReviewRepository;

    /**
     * @param RepositoryInterface $productReviewRepository
     */
    public function __construct(RepositoryInterface $productReviewRepository)
    {
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * @Transform :productReview
     */
    public function getProductReviewByTitle($productReviewTitle)
    {
        $productReview = $this->productReviewRepository->findOneBy(['title' => $productReviewTitle]);

        Assert::notNull(
            $productReview,
            sprintf('Product review with title "%s" does not exist', $productReviewTitle)
        );

        return $productReview;
    }
}
