<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\ProductReview\UpdatePageInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
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
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     */
    public function __construct(IndexPageInterface $indexPage, UpdatePageInterface $updatePage)
    {
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @When I want to browse product reviews
     */
    public function iWantToBrowseProductReviews()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should (also) see the product review :title in the list
     */
    public function iShouldSeeTheProductReviewTitleInTheList($title)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Product review with a title %s should exist but it does not.', $title)
        );
    }

    /**
     * @Then I should see :amount product reviews in the list
     */
    public function iShouldSeeProductReviewsInTheList($amount)
    {
        Assert::same(
            (int) $amount,
            $this->indexPage->countItems(),
            '%2$s rows with product reviews should appear on page, %s rows has been found'
        );
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
     */
    public function iChangeItsTitleTo($title)
    {
        $this->updatePage->specifyTitle($title);
    }

    /**
     * @When I change its comment to :comment
     */
    public function iChangeItsCommentTo($comment)
    {
        $this->updatePage->specifyComment($comment);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
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
        Assert::same(
            $rating,
            $this->updatePage->getRating(),
            'Product review should have rating %s, but it has %s'
        );
    }

    /**
     * @When I choose :rating as its rating
     */
    public function iChooseAsItsRating($rating)
    {
        $this->updatePage->chooseRating($rating);
    }

    /**
     * @Then I should see the product :productName
     */
    public function iShouldSeeTheProduct($productName)
    {
        Assert::same(
            $productName,
            $this->updatePage->getProductName(),
            'Product should have name %s, but it has %s'
        );
    }

    /**
     * @Then I should see the customer's name :customerName
     */
    public function iShouldSeeTheCustomerSName($customerName)
    {
        Assert::same(
            $customerName,
            $this->updatePage->getCustomerName(),
            'Customer should have name %s, but they have %s'
        );
    }

    /**
     * @param string $element
     * @param string $value
     */
    private function assertElementValue($element, $value)
    {
        Assert::true(
            $this->updatePage->hasResourceValues([$element => $value]),
            sprintf('Product review should have %s with %s value.', $element, $value)
        );
    }
}
