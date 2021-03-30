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
use Sylius\Component\Review\Model\ReviewInterface;
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
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I want to review product :product
     */
    public function iWantToReviewProduct(ProductInterface $product): void
    {
        $this->client->buildCreateRequest();
        $this->client->addRequestData('productCode', $product->getCode());
    }

    /**
     * @When I leave a comment :comment, titled :title as :email
     * @When I leave a comment :comment, titled :title
     */
    public function iLeaveACommentTitled(string $comment, string $title, ?string $email = null): void
    {
        $this->client->addRequestData('title', $title);
        $this->client->addRequestData('comment', $comment);
        $this->client->addRequestData('email', $email);
    }

    /**
     * @Then I should see :amount product reviews
     */
    public function iShouldSeeProductReviews(int $amount = 0): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        $this->client->index();
        $this->client->addFilter('reviewSubject', $this->iriConverter->getIriFromItem($product));
        $this->client->addFilter('itemsPerPage', 3);
        $this->client->addFilter('order[createdAt]', 'desc');
        $this->client->filter();

        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $amount);
    }

    /**
     * @Then I should see reviews titled :titleOne, :titleTwo and :titleThree
     */
    public function iShouldSeeReviewsTitledAnd(string ...$titles): void
    {
        Assert::true($this->hasReviewsWithTitles($titles));
    }

    /**
     * @Then I should not see review titled :title
     */
    public function iShouldNotSeeReviewTitled(string $title): void
    {
        Assert::false($this->hasReviewsWithTitles([$title]));
    }

    /**
     * @When I rate it with :rating point(s)
     */
    public function iRateItWithPoints(int $rating): void
    {
        $this->client->addRequestData('rating', $rating);
    }

    /**
     * @Then I should be notified that my review is waiting for the acceptation
     */
    public function iShouldBeNotifiedThatMyReviewIsWaitingForTheAcceptation(): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'status'),
            ReviewInterface::STATUS_NEW
        );
    }

    /**
     * @Then I should see :amount product reviews in the list
     * @Then I should be notified that there are no reviews
     */
    public function iShouldSeeProductReviewsInTheList(int $amount = 0): void
    {
        $productReviews = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::count($productReviews, $amount);
    }

    /**
     * @Then I should not see review titled :title in the list
     */
    public function iShouldNotSeeReviewTitledInTheList(string $title): void
    {
        Assert::isEmpty($this->responseChecker->getCollectionItemsWithValue($this->client->getLastResponse(), 'title', $title));
    }

    private function hasReviewsWithTitles(array $titles): bool
    {
        foreach ($titles as $title) {
            if (!$this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'title', $title)) {
                return false;
            }
        }

        return true;
    }
}
