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
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
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
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should (also) see the product review :title in the list
     */
    public function iShouldSeeTheProductReviewTitleInTheList($title): void
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
     * @When I want to modify the :productReview product review
     */
    public function iWantToModifyTheProductReview(ReviewInterface $productReview): void
    {
        $this->updatePage->open(['id' => $productReview->getId()]);
    }

    /**
     * @When I change its title to :title
     * @When I remove its title
     */
    public function iChangeItsTitleTo($title = null): void
    {
        $this->updatePage->specifyTitle($title);
    }

    /**
     * @When I change its comment to :comment
     * @When I remove its comment
     */
    public function iChangeItsCommentTo($comment = null): void
    {
        $this->updatePage->specifyComment($comment);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then /^this product review (comment|title) should be "([^"]+)"$/
     */
    public function thisProductReviewElementShouldBeValue($element, $value): void
    {
        $this->assertElementValue($element, $value);
    }

    /**
     * @Then this product review rating should be :rating
     */
    public function thisProductReviewRatingShouldBe($rating): void
    {
        Assert::same($this->updatePage->getRating(), $rating);
    }

    /**
     * @When I choose :rating as its rating
     */
    public function iChooseAsItsRating($rating): void
    {
        $this->updatePage->chooseRating($rating);
    }

    /**
     * @Then I should be editing review of product :productName
     */
    public function iShouldBeEditingReviewOfProduct($productName): void
    {
        Assert::same($this->updatePage->getProductName(), $productName);
    }

    /**
     * @Then I should see the customer's name :customerName
     */
    public function iShouldSeeTheCustomerSName($customerName): void
    {
        Assert::same($this->updatePage->getCustomerName(), $customerName);
    }

    /**
     * @When I accept the :productReview product review
     */
    public function iAcceptTheProductReview(ReviewInterface $productReview): void
    {
        $this->indexPage->accept(['title' => $productReview->getTitle()]);
    }

    /**
     * @When I reject the :productReview product review
     */
    public function iRejectTheProductReview(ReviewInterface $productReview): void
    {
        $this->indexPage->reject(['title' => $productReview->getTitle()]);
    }

    /**
     * @Then /^(this product review) status should be "([^"]+)"$/
     */
    public function thisProductReviewStatusShouldBe(ReviewInterface $productReview, $status): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage([
            'title' => $productReview->getTitle(),
            'status' => $status,
        ]));
    }

    /**
     * @Then /^I should be notified that it has been successfully (accepted|rejected)$/
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyUpdated($action): void
    {
        $this->notificationChecker->checkNotification(
            sprintf('Review has been successfully %s.', $action),
            NotificationType::success()
        );
    }

    /**
     * @When I delete the :productReview product review
     */
    public function iDeleteTheProductReview(ReviewInterface $productReview): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['title' => $productReview->getTitle()]);
    }

    /**
     * @Then /^(this product review) should no longer exist in the registry$/
     */
    public function thisProductReviewShouldNoLongerExistInTheRegistry(ReviewInterface $productReview): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['title' => $productReview->getTitle()]));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired($element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Review %s should not be blank.', $element));
    }

    /**
     * @Then /^this product review should still be titled "([^"]+)"$/
     */
    public function thisProductReviewTitleShouldBeTitled($productReviewTitle): void
    {
        $this->iWantToBrowseProductReviews();

        Assert::true($this->indexPage->isSingleResourceOnPage(['title' => $productReviewTitle]));
    }

    /**
     * @Then /^(this product review) should still have a comment "([^"]+)"$/
     */
    public function thisProductReviewShouldStillHaveAComment(ReviewInterface $productReview, $comment): void
    {
        $this->iWantToModifyTheProductReview($productReview);

        $this->assertElementValue('comment', $comment);
    }

    /**
     * @param string $element
     * @param string $value
     */
    private function assertElementValue(string $element, string $value): void
    {
        Assert::true($this->updatePage->hasResourceValues([$element => $value]));
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        Assert::same($this->updatePage->getValidationMessage($element), $expectedMessage);
    }
}
