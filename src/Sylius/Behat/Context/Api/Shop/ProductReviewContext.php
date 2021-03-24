<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Shop;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ProductReviewContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @When I check this product's reviews
     */
    public function iCheckThisProductsReviews(): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        $this->client->index();
        $this->client->addFilter('reviewSubject', $this->iriConverter->getIriFromItem($product));
        $this->client->filter();
    }

    /**
     * @Then I should see :amount product reviews in the list
     */
    public function iShouldSeeProductReviewsInTheList(int $amount): void
    {
        $result = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same(sizeof($result), $amount);
    }

    /**
     * @Then I should not see review titled :title in the list
     */
    public function iShouldNotSeeReviewTitledInTheList(string $title): void
    {
        $result = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::null($this->findReviewByTitle($title, $result));
    }

    /**
     * @Then I should be notified that there are no reviews
     */
    public function iShouldBeNotifiedThatThereAreNoReviews(): void
    {
        $result = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same(sizeof($result), 0);
    }

    private function findReviewByTitle(string $title, array $reviews): ?array
    {
        /** @var array $review */
        foreach ($reviews as $review) {
            if ($review['title'] === $title) {
                return $review;
            }
        }

        return null;
    }
}
