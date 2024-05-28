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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ProductReviewContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private FactoryInterface $productReviewFactory,
        private RepositoryInterface $productReviewRepository,
        private StateMachineInterface $stateMachine,
    ) {
    }

    /**
     * @Given /^(this product) has one review from (customer "[^"]+")$/
     */
    public function productHasAReview(ProductInterface $product, CustomerInterface $customer): void
    {
        $review = $this->createProductReview($product, 'Title', 5, 'Comment', $customer);

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has(?:| also) a review titled "([^"]+)" and rated (\d+) added by (customer "[^"]+")(?:|, created (\d+) days ago)$/
     * @Given /^(this product) has(?:| also) an accepted review titled "([^"]+)" and rated (\d+) added by (customer "[^"]+")(?:|, created (\d+) days ago)$/
     */
    public function thisProductHasAnAcceptedReviewTitledAndRatedAddedByCustomer(
        ProductInterface $product,
        string $title,
        int $rating,
        CustomerInterface $customer,
        ?int $daysSinceCreation = null,
    ): void {
        $review = $this->createProductReview($product, $title, $rating, $title, $customer);
        if (null !== $daysSinceCreation) {
            $review->setCreatedAt(new \DateTime('-' . $daysSinceCreation . ' days'));
        }

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has(?:| also) a rejected review titled "([^"]+)" and rated (\d+) added by (customer "[^"]+")(?:|, created (\d+) days ago)$/
     */
    public function thisProductHasARejectedReviewTitledAndRatedAddedByCustomer(
        ProductInterface $product,
        string $title,
        int $rating,
        CustomerInterface $customer,
        ?int $daysSinceCreation = null,
    ): void {
        $review = $this->createProductReview($product, $title, $rating, $title, $customer, ProductReviewTransitions::TRANSITION_REJECT);
        if (null !== $daysSinceCreation) {
            $review->setCreatedAt(new \DateTime('-' . $daysSinceCreation . ' days'));
        }

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has(?:| also) a new review titled "([^"]+)" and rated (\d+) added by (customer "[^"]+")(?:|, created (\d+) days ago)$/
     */
    public function thisProductHasANewReviewTitledAndRatedAddedByCustomer(
        ProductInterface $product,
        string $title,
        int $rating,
        CustomerInterface $customer,
        ?int $daysSinceCreation = null,
    ): void {
        $review = $this->createProductReview($product, $title, $rating, $title, $customer, null);
        if (null !== $daysSinceCreation) {
            $review->setCreatedAt(new \DateTime('-' . $daysSinceCreation . ' days'));
        }

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has(?:| also) a review titled "([^"]+)" and rated (\d+) with a comment "([^"]+)" added by (customer "[^"]+")$/
     */
    public function thisProductHasAReviewTitledAndRatedWithACommentAddedByCustomer(
        ProductInterface $product,
        string $title,
        int $rating,
        string $comment,
        CustomerInterface $customer,
    ): void {
        $review = $this->createProductReview($product, $title, $rating, $comment, $customer);

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product)(?:| also) has accepted reviews rated (\d+), (\d+), (\d+), (\d+) and (\d+)$/
     * @Given /^(this product)(?:| also) has accepted reviews rated (\d+), (\d+) and (\d+)$/
     */
    public function thisProductHasAcceptedReviewsRated(ProductInterface $product, int ...$rates): void
    {
        $customer = $this->sharedStorage->get('customer');
        foreach ($rates as $key => $rate) {
            $review = $this->createProductReview($product, 'Title ' . $key, $rate, 'Comment ' . $key, $customer);
            $this->productReviewRepository->add($review);
        }
    }

    /**
     * @Given /^(this product)(?:| also) has review rated (\d+) which is not accepted yet$/
     */
    public function itAlsoHasReviewRatedWhichIsNotAcceptedYet(ProductInterface $product, int $rate): void
    {
        $customer = $this->sharedStorage->get('customer');
        $review = $this->createProductReview($product, 'Title', $rate, 'Comment', $customer, null);
        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) also has review rated (\d+) which is rejected$/
     */
    public function itAlsoHasReviewRatedWhichIsRejected(ProductInterface $product, int $rate): void
    {
        $customer = $this->sharedStorage->get('customer');
        $review = $this->createProductReview($product, 'Title', $rate, 'Comment', $customer, ProductReviewTransitions::TRANSITION_REJECT);
        $this->productReviewRepository->add($review);
    }

    /**
     * @param string $title
     * @param int $rating
     * @param string $comment
     * @param string $transition
     *
     * @return ReviewInterface
     */
    private function createProductReview(
        ProductInterface $product,
        string $title,
        int $rating,
        string $comment,
        ?CustomerInterface $customer = null,
        ?string $transition = ProductReviewTransitions::TRANSITION_ACCEPT,
    ) {
        /** @var ReviewInterface $review */
        $review = $this->productReviewFactory->createNew();
        $review->setTitle($title);
        $review->setRating($rating);
        $review->setComment($comment);
        $review->setReviewSubject($product);
        $review->setAuthor($customer);

        $product->addReview($review);

        if (null !== $transition) {
            $this->stateMachine->apply($review, ProductReviewTransitions::GRAPH, $transition);
        }

        $this->sharedStorage->set('product_review', $review);

        return $review;
    }
}
