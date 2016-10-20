<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class ProductReviewContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $productReviewFactory;

    /**
     * @var RepositoryInterface
     */
    private $productReviewRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $productReviewFactory
     * @param RepositoryInterface $productReviewRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productReviewFactory,
        RepositoryInterface $productReviewRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productReviewFactory = $productReviewFactory;
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * @Given /^(this product) has one review$/
     */
    public function productHasAReview(ProductInterface $product)
    {
        $review = $this->createProductReview($product, 'Title', 5);

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has(?:| also) a review titled "([^"]+)" and rated (\d+) with a comment "([^"]+)" added by (customer "[^"]+")$/
     */
    public function thisProductHasAReviewTitledAndRatedWithACommentAddedByCustomer(
        ProductInterface $product,
        $title,
        $rating,
        $comment,
        CustomerInterface $customer
    ) {
        $review = $this->createProductReview($product, $title, $rating, $comment, $customer);

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has(?:| also) a review titled "([^"]+)" and rated (\d+) with a comment "([^"]+)" added by (customer "[^"]+") which is not accepted yet$/
     */
    public function thisProductHasAReviewTitledAndRatedWithACommentAddedByCustomerWhichIsNotAcceptedYet(
        ProductInterface $product,
        $title,
        $rating,
        $comment,
        CustomerInterface $customer
    ) {
        $review = $this->createProductReview($product, $title, $rating, $comment, $customer, ReviewInterface::STATUS_NEW);

        $this->productReviewRepository->add($review);
    }

    /**
     * @param ProductInterface $product
     * @param string $title
     * @param int $rating
     * @param string|null $comment
     * @param CustomerInterface|null $customer
     * @param string $status
     *
     * @return ReviewInterface
     */
    private function createProductReview(
        ProductInterface $product,
        $title,
        $rating,
        $comment = null,
        CustomerInterface $customer = null,
        $status = ReviewInterface::STATUS_ACCEPTED
    ) {
        /** @var ReviewInterface $review */
        $review = $this->productReviewFactory->createNew();
        $review->setTitle($title);
        $review->setRating($rating);
        $review->setComment($comment);
        $review->setReviewSubject($product);
        $review->setAuthor($customer);
        $review->setStatus($status);

        $product->addReview($review);

        $this->sharedStorage->set('product_review', $review);

        return $review;
    }
}
