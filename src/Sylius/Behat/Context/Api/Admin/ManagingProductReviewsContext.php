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
use Sylius\Behat\Client\ApiPlatformStateMachineClientInterface;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Review\Model\ReviewInterface;
use Webmozart\Assert\Assert;

final class ManagingProductReviewsContext implements Context
{
    /** @var string */
    private $resource;

    /** @var ApiClientInterface */
    private $client;

    /** @var ApiPlatformStateMachineClientInterface */
    private $stateMachineClient;

    /** @var SharedStorage */
    private $sharedStorage;

    public function __construct(
        string $resource,
        ApiClientInterface $client,
        ApiPlatformStateMachineClientInterface $stateMachineClient,
        SharedStorageInterface $sharedStorage
    ) {
        $this->resource = $resource;
        $this->client = $client;
        $this->stateMachineClient = $stateMachineClient;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I browse product reviews
     * @When I want to browse product reviews
     */
    public function iWantToBrowseProductReviews(): void
    {
        $this->client->index($this->resource);
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
     * @When I want to modify the :productReview product review
     */
    public function iWantToModifyTheProductReview(ReviewInterface $productReview): void
    {
        $this->client->buildUpdateRequest($this->resource, (string) $productReview->getId());
    }

    /**
     * @When I change its title to :title
     */
    public function iChangeItsTitleTo(?string $title = null): void
    {
        if ($title !== null) {
            $this->client->addRequestData('title', $title);
        }
    }

    /**
     * @When I change its comment to :comment
     */
    public function iChangeItsCommentTo(?string $comment = null): void
    {
        if ($comment !== null) {
            $this->client->addRequestData('comment', $comment);
        }
    }

    /**
     * @When /^I remove its (comment|title)$/
     */
    public function iRemoveItsComment(string $element): void
    {
        $this->client->addRequestData($element, '');
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
     * @Then /^(this product review) (comment|title) should be "([^"]+)"$/
     */
    public function thisProductReviewElementShouldBeValue(ReviewInterface $productReview, string $element, string $value): void
    {
        $this->assertIfReviewHasElementWithValue($productReview, $element, $value);
    }

    /**
     * @Then (this product review) rating should be :rating
     */
    public function thisProductReviewRatingShouldBe(ReviewInterface $productReview, string $rating): void
    {
        $this->assertIfReviewHasElementWithValue($productReview, 'rating', $rating);
    }

    /**
     * @When I choose :rating as its rating
     */
    public function iChooseAsItsRating(string $rating): void
    {
        $this->client->buildUpdateRequest('rating', $rating);
    }

    /**
     * @Then I should be editing review of product :productName
     */
    public function iShouldBeEditingReviewOfProduct(string $productName): void
    {
        $name = $this->client->getCollection()['reviewSubject']['name'];
        Assert::same($name, $productName);
    }

    /**
     * @Then I should see the customer's name :customerName
     */
    public function iShouldSeeTheCustomerSName(string $customerName): void
    {
        $name = $this->client->getCollection()['author']['fullName'];
        Assert::same($name, $customerName);
    }

    /**
     * @When I accept the :productReview product review
     */
    public function iAcceptTheProductReview(ReviewInterface $productReview): void
    {
        $this->applyTransition((string) $productReview->getId(), ProductReviewTransitions::TRANSITION_ACCEPT);
    }

    /**
     * @When I reject the :productReview product review
     */
    public function iRejectTheProductReview(ReviewInterface $productReview): void
    {
        $this->applyTransition((string) $productReview->getId(), ProductReviewTransitions::TRANSITION_REJECT);
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
     * @When I delete the :productReview product review
     */
    public function iDeleteTheProductReview(ReviewInterface $productReview): void
    {
        $this->sharedStorage->set('product_review_id', $productReview->getId());

        $this->client->delete($this->resource, (string) $productReview->getId());
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

    /**
     * @Then /^average rating of (product "[^"]+") should be (\d+)$/
     */
    public function thisProductAverageRatingShouldBe(ProductInterface $product, int $averageRating): void
    {
        // todo, this step should be in product context which will be covered in separate RP
    }

    private function isItemOnIndex(string $property, string $value): bool
    {
        $this->client->index($this->resource);

        return $this->client->hasItemWithValue($property, $value);
    }

    private function assertIfReviewHasElementWithValue(ReviewInterface $productReview, string $element, string $value): void
    {
        $this->client->show($this->resource, (string) $productReview->getId());
        Assert::true(
            $this->client->hasValue($element, $value),
            sprintf('Product review %s is not %s', $element, $value)
        );
    }

    private function applyTransition(string $id, string $transition): void
    {
        $this->stateMachineClient->applyTransition($this->resource, $id, $transition);
    }
}
