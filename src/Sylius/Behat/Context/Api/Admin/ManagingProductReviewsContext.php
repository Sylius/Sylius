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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Webmozart\Assert\Assert;

final class ManagingProductReviewsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I browse product reviews
     * @When I want to browse product reviews
     */
    public function iWantToBrowseProductReviews(): void
    {
        $this->client->index('product_reviews');
    }

    /**
     * @When I want to modify the :productReview product review
     */
    public function iWantToModifyTheProductReview(ReviewInterface $productReview): void
    {
        $this->client->buildUpdateRequest('product_reviews', (string) $productReview->getId());
    }

    /**
     * @When I change its title to :title
     * @when I remove its title
     */
    public function iChangeItsTitleTo(?string $title = ''): void
    {
        $this->client->addRequestData('title', $title);
    }

    /**
     * @When I change its comment to :comment
     * @when I remove its comment
     */
    public function iChangeItsCommentTo(?string $comment = ''): void
    {
        $this->client->updateRequestData(['comment' => $comment]);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @When I choose :rating as its rating
     */
    public function iChooseAsItsRating(int $rating): void
    {
        $this->client->updateRequestData(['rating' => $rating]);
    }

    /**
     * @When /^I (accept|reject) the ("([^"]+)" product review)$/
     */
    public function iChangeStateTheProductReview(string $state, ReviewInterface $productReview): void
    {
        $this->client->applyTransition('product_reviews', (string) $productReview->getId(), $state);
    }

    /**
     * @When I delete the :productReview product review
     */
    public function iDeleteTheProductReview(ReviewInterface $productReview): void
    {
        $this->sharedStorage->set('product_review_id', $productReview->getId());

        $this->client->delete('product_reviews', (string) $productReview->getId());
    }

    /**
     * @Then I should (also) see the product review :title in the list
     */
    public function iShouldSeeTheProductReviewTitleInTheList(string $title): void
    {
        Assert::true(
            $this->isItemOnIndex('title', $title),
            sprintf('Product review with title %s does not exist', $title)
        );
    }

    /**
     * @Then I should see a single product review in the list
     * @Then I should see :amount reviews in the list
     */
    public function iShouldSeeReviewsInTheList(int $amount = 1): void
    {
        Assert::same($this->client->countCollectionItems(), $amount);
    }

    /**
     * @Then /^(this product review) (comment|title) should be "([^"]+)"$/
     */
    public function thisProductReviewElementShouldBeValue(ReviewInterface $productReview, string $element, string $value): void
    {
        $this->assertIfReviewHasElementWithValue($productReview, $element, $value);
    }

    /**
     * @Then /^(this product review) rating should be (\d+)$/
     */
    public function thisProductReviewRatingShouldBe(ReviewInterface $productReview, int $rating): void
    {
        $this->assertIfReviewHasElementWithValue($productReview, 'rating', $rating);
    }

    /**
     * @Then /^(this product review) status should be "([^"]+)"$/
     */
    public function thisProductReviewStatusShouldBe(ReviewInterface $productReview, string $status): void
    {
        $this->assertIfReviewHasElementWithValue($productReview, 'status', $status);
    }

    /**
     * @Then /^I should be notified that it has been successfully (accepted|rejected)$/
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyUpdated(string $action): void
    {
        $this->assertIfReviewHasElementWithValue($this->sharedStorage->get('product_review'), 'status', $action);
    }

    /**
     * @Then this product review should no longer exist in the registry
     */
    public function thisProductReviewShouldNoLongerExistInTheRegistry(): void
    {
       $id = (string) $this->sharedStorage->get('product_review_id');
       Assert::false(
           $this->isItemOnIndex('id', $id),
           sprintf('Product review with id %s exist', $id)
       );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        Assert::contains($this->client->getError(), sprintf('%s: Review %s should not be blank', $element, $element));
    }

    /**
     * @Then /^(this product review) should still be titled "([^"]+)"$/
     */
    public function thisProductReviewTitleShouldBeTitled(ReviewInterface $productReview, string $title): void
    {
        $this->assertIfReviewHasElementWithValue($productReview, 'title', $title);
    }

    /**
     * @Then /^(this product review) should still have a comment "([^"]+)"$/
     */
    public function thisProductReviewShouldStillHaveAComment(ReviewInterface $productReview, string $comment): void
    {
        $this->assertIfReviewHasElementWithValue($productReview, 'comment', $comment);
    }

    private function isItemOnIndex(string $property, string $value): bool
    {
        $this->client->index('product_reviews');

        return $this->client->hasItemWithValue($property, $value);
    }

    /** @param string|int $value */
    private function assertIfReviewHasElementWithValue(ReviewInterface $productReview, string $element, $value): void
    {
        $this->client->show('product_reviews', (string) $productReview->getId());
        Assert::true(
            $this->client->responseHasValue($element, $value),
            sprintf('Product review %s is not %s', $element, $value)
        );
    }
}
