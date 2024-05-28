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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\ProductReview\IndexPageInterface;
use Sylius\Behat\Page\Admin\ProductReview\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Webmozart\Assert\Assert;

final class ManagingProductReviewsContext implements Context
{
    public function __construct(
        private IndexPageInterface $indexPage,
        private UpdatePageInterface $updatePage,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @Given I am browsing product reviews
     * @When I browse product reviews
     * @When I want to browse product reviews
     */
    public function iWantToBrowseProductReviews(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I check (also) the :productReviewTitle product review
     */
    public function iCheckTheProductReview(string $productReviewTitle): void
    {
        $this->indexPage->checkResourceOnPage(['title' => $productReviewTitle]);
    }

    /**
     * @When I choose :state as a status filter
     */
    public function iChooseStateAsStatusFilter(string $state): void
    {
        $this->indexPage->filterByState($state);
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    /**
     * @When I sort the product reviews :sortingOrder by :field
     */
    public function iSortProductReviewsBy(string $sortingOrder, string $field): void
    {
        $this->indexPage->sortBy($field, $sortingOrder === 'descending' ? 'desc' : 'asc');
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @When I want to modify the :productReview product review
     */
    public function iWantToModifyTheProductReview(ReviewInterface $productReview)
    {
        $this->updatePage->open(['id' => $productReview->getId()]);
    }

    /**
     * @When I change its title to :title
     * @When I remove its title
     */
    public function iChangeItsTitleTo($title = null)
    {
        $this->updatePage->specifyTitle($title ?? '');
    }

    /**
     * @When I change its comment to :comment
     * @When I remove its comment
     */
    public function iChangeItsCommentTo($comment = null)
    {
        $this->updatePage->specifyComment($comment ?? '');
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I choose :rating as its rating
     */
    public function iChooseAsItsRating($rating)
    {
        $this->updatePage->chooseRating($rating);
    }

    /**
     * @When I accept the :productReview product review
     */
    public function iAcceptTheProductReview(ReviewInterface $productReview)
    {
        $this->indexPage->accept(['title' => $productReview->getTitle()]);
    }

    /**
     * @When I reject the :productReview product review
     */
    public function iRejectTheProductReview(ReviewInterface $productReview)
    {
        $this->indexPage->reject(['title' => $productReview->getTitle()]);
    }

    /**
     * @Then I should (also) see the product review :title in the list
     */
    public function iShouldSeeTheProductReviewTitleInTheList($title)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['title' => $title]));
    }

    /**
     * @Then I should see a single product review in the list
     * @Then I should see :amount reviews in the list
     */
    public function iShouldSeeReviewsInTheList(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amount);
    }

    /**
     * @Then /^this product review (comment|title) should be "([^"]+)"$/
     */
    public function thisProductReviewElementShouldBeValue($element, $value)
    {
        $this->assertElementValue($element, $value);
    }

    /**
     * @Then this product review rating should be :rating
     */
    public function thisProductReviewRatingShouldBe($rating)
    {
        Assert::same($this->updatePage->getRating(), $rating);
    }

    /**
     * @Then I should be editing review of product :productName
     */
    public function iShouldBeEditingReviewOfProduct($productName)
    {
        Assert::same($this->updatePage->getProductName(), $productName);
    }

    /**
     * @Then I should see the customer's name :customerName
     */
    public function iShouldSeeTheCustomerSName($customerName)
    {
        Assert::same($this->updatePage->getCustomerName(), $customerName);
    }

    /**
     * @Then /^(this product review) status should be "([^"]+)"$/
     */
    public function thisProductReviewStatusShouldBe(ReviewInterface $productReview, $status)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage([
            'title' => $productReview->getTitle(),
            'status' => $status,
        ]));
    }

    /**
     * @Then /^I should be notified that it has been successfully (accepted|rejected)$/
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyUpdated($action)
    {
        $this->notificationChecker->checkNotification(
            sprintf('Review has been successfully %s.', $action),
            NotificationType::success(),
        );
    }

    /**
     * @When I delete the :productReview product review
     */
    public function iDeleteTheProductReview(ReviewInterface $productReview)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['title' => $productReview->getTitle()]);
    }

    /**
     * @Then /^(this product review) should no longer exist in the registry$/
     */
    public function thisProductReviewShouldNoLongerExistInTheRegistry(ReviewInterface $productReview)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['title' => $productReview->getTitle()]));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Review %s should not be blank.', $element));
    }

    /**
     * @Then /^this product review should still be titled "([^"]+)"$/
     */
    public function thisProductReviewTitleShouldBeTitled($productReviewTitle)
    {
        $this->iWantToBrowseProductReviews();

        Assert::true($this->indexPage->isSingleResourceOnPage(['title' => $productReviewTitle]));
    }

    /**
     * @Then /^(this product review) should still have a comment "([^"]+)"$/
     */
    public function thisProductReviewShouldStillHaveAComment(ReviewInterface $productReview, $comment)
    {
        $this->iWantToModifyTheProductReview($productReview);

        $this->assertElementValue('comment', $comment);
    }

    /**
     * @Then the first product review in the list should have title :title
     */
    public function theFirstProductReviewInTheListShouldHaveTitle(string $title): void
    {
        $titles = $this->indexPage->getColumnFields('title');

        Assert::contains(reset($titles), $title);
    }

    /**
     * @Then the last product review in the list should have title :title
     */
    public function theLastProductReviewInTheListShouldHaveTitle(string $title): void
    {
        $titles = $this->indexPage->getColumnFields('title');

        Assert::contains(end($titles), $title);
    }

    private function assertElementValue(string $element, string $value): void
    {
        Assert::true($this->updatePage->hasResourceValues([$element => $value]));
    }

    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        Assert::same($this->updatePage->getValidationMessage($element), $expectedMessage);
    }
}
