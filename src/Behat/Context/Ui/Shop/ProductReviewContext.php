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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\ProductReview\CreatePageInterface;
use Sylius\Behat\Page\Shop\ProductReview\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Webmozart\Assert\Assert;

final class ProductReviewContext implements Context
{
    public function __construct(
        private readonly CreatePageInterface $createPage,
        private readonly NotificationCheckerInterface $notificationChecker,
        private readonly IndexPageInterface $indexPage,
    ) {
    }

    /**
     * @When I want to review product :product
     */
    public function iWantToReviewProduct(ProductInterface $product): void
    {
        $this->createPage->open(['slug' => $product->getSlug()]);
    }

    /**
     * @When I leave a comment :comment as :author
     * @When I leave a comment :comment, titled :title
     * @When I leave a comment :comment, titled :title as :author
     * @When I leave a review titled :title as :author
     */
    public function iLeaveACommentTitled(?string $comment = null, ?string $title = null, ?string $author = null): void
    {
        $this->createPage->titleReview($title);
        $this->createPage->setComment($comment);

        if (null !== $author) {
            $this->createPage->setAuthor($author);
        }
    }

    /**
     * @When I title it with very long title
     */
    public function iTitleItWithVeryLongTitle(): void
    {
        $this->createPage->titleReview($this->getVeryLongTitle());
    }

    /**
     * @When I rate it with :rate point(s)
     */
    public function iRateItWithPoints(int $rate): void
    {
        $this->createPage->rateReview($rate);
    }

    /**
     * @When I do not rate it
     */
    public function iDoNotRateIt(): void
    {
        // intentionally left blank, as review rate is not selected by default
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->submitReview();
    }

    /**
     * @Then I should be notified that my review is waiting for the acceptation
     */
    public function iShouldBeNotifiedThatMyReviewIsWaitingForTheAcceptation(): void
    {
        $this->notificationChecker->checkNotification(
            'Your review is waiting for the acceptation.',
            NotificationType::success(),
        );
    }

    /**
     * @Then I should be notified that I must check review rating
     */
    public function iShouldBeNotifiedThatIMustCheckReviewRating(): void
    {
        Assert::same($this->createPage->getRateValidationMessage(), 'You must check review rating.');
    }

    /**
     * @Then I should be notified that title is required
     */
    public function iShouldBeNotifiedThatTitleIsRequired(): void
    {
        Assert::same($this->createPage->getTitleValidationMessage(), 'Review title should not be blank.');
    }

    /**
     * @Then I should be notified that title must have at least 2 characters
     */
    public function iShouldBeNotifiedThatTitleMustHaveAtLeast2Characters(): void
    {
        Assert::same($this->createPage->getTitleValidationMessage(), 'Review title must have at least 2 characters.');
    }

    /**
     * @Then I should be notified that title must have at most 255 characters
     */
    public function iShouldBeNotifiedThatTitleMustHaveAtMost255Characters(): void
    {
        Assert::same($this->createPage->getTitleValidationMessage(), 'Review title must have at most 255 characters.');
    }

    /**
     * @Then I should be notified that comment is required
     */
    public function iShouldBeNotifiedThatCommentIsRequired(): void
    {
        Assert::same($this->createPage->getCommentValidationMessage(), 'Review comment should not be blank.');
    }

    /**
     * @Then I should be notified that I must enter my email
     */
    public function iShouldBeNotifiedThatIMustEnterMyEmail(): void
    {
        Assert::same($this->createPage->getAuthorValidationMessage(), 'Please enter your email.');
    }

    /**
     * @Then I should be notified that this email is already registered
     */
    public function iShouldBeNotifiedThatThisEmailIsAlreadyRegistered(): void
    {
        Assert::same(
            $this->createPage->getAuthorValidationMessage(),
            'This email is already registered, please login or use forgotten password.',
        );
    }

    /**
     * @Then the :productReview product review of :product product should not be visible for customers
     */
    public function thisProductReviewOfProductShouldNotBeVisibleForCustomers(
        ReviewInterface $productReview,
        ProductInterface $product,
    ): void {
        $this->indexPage->open(['slug' => $product->getSlug()]);
        Assert::false($this->indexPage->hasReviewTitled($productReview->getTitle()));
    }

    private function getVeryLongTitle(): string
    {
        return 'Exegi monumentum aere perennius regalique situ pyramidum altius, quod non imber edax, non Aquilo inpotens possit diruere aut innumerabilis annorum series et fuga temporum. Non omnis moriar multaque pars mei vitabit Libitinam; usque ego postera crescam laude recens, dum Capitoliumscandet cum tacita virgine pontifex.Dicar, qua violens obstrepit Aufiduset qua pauper aquae Daunus agrestiumregnavit populorum, ex humili potensprinceps Aeolium carmen ad Italosdeduxisse modos. Sume superbiamquaesitam meritis et mihi Delphicalauro cinge volens, Melpomene, comam.';
    }
}
