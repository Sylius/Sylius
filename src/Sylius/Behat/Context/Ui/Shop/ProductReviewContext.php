<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\ProductReview\CreatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductReviewContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param CreatePageInterface $createPage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        CreatePageInterface $createPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to review product :product
     */
    public function iWantToReviewProduct(ProductInterface $product)
    {
        $this->createPage->open(['slug' => $product->getSlug()]);
    }

    /**
     * @When I leave a comment :comment as :author
     * @When I leave a comment :comment, titled :title
     * @When I leave a comment :comment, titled :title as :author
     * @When I leave a review titled :title as :author
     */
    public function iLeaveACommentTitled($comment = null, $title = null, $author = null)
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
    public function iTitleItWithVeryLongTitle()
    {
        $this->createPage->titleReview($this->getVeryLongTitle());
    }

    /**
     * @When I rate it with :rate point(s)
     */
    public function iRateItWithPoints($rate)
    {
        $this->createPage->rateReview($rate);
        $this->createPage->submitReview();
    }

    /**
     * @Then I should be notified that my review is waiting for the acceptation
     */
    public function iShouldBeNotifiedThatMyReviewIsWaitingForTheAcceptation()
    {
        $this->notificationChecker->checkNotification('Your review is waiting for the acceptation.', NotificationType::success());
    }

    /**
     * @When I do not rate it
     */
    public function iDoNotRateIt()
    {
        $this->createPage->submitReview();
    }

    /**
     * @Then I should be notified that I must check review rating
     */
    public function iShouldBeNotifiedThatIMustCheckReviewRating()
    {
        Assert::same(
            $this->createPage->getRateValidationMessage(),
            'You must check review rating.',
            'There should be rate validation error, but there is not.'
        );
    }

    /**
     * @Then I should be notified that title is required
     */
    public function iShouldBeNotifiedThatTitleIsRequired()
    {
        Assert::same(
            $this->createPage->getTitleValidationMessage(),
            'Review title should not be blank.',
            'There should be title validation error, but there is not.'
        );
    }

    /**
     * @Then I should be notified that title is too short
     */
    public function iShouldBeNotifiedThatTitleIsTooShort()
    {
        Assert::same(
            $this->createPage->getTitleValidationMessage(),
            'Review title must have at least 2 characters.',
            'There should be title length validation error, but there is not.'
        );
    }

    /**
     * @Then I should be notified that title is too long
     */
    public function iShouldBeNotifiedThatTitleIsTooLong()
    {
        Assert::same(
            $this->createPage->getTitleValidationMessage(),
            'Review title must have at most 255 characters.',
            'There should be title length validation error, but there is not.'
        );
    }

    /**
     * @Then I should be notified that comment is required
     */
    public function iShouldBeNotifiedThatCommentIsRequired()
    {
        Assert::same(
            $this->createPage->getCommentValidationMessage(),
            'Review comment should not be blank.',
            'There should be comment validation error, but there is not.'
        );
    }

    /**
     * @Then I should be notified that I must enter my email
     */
    public function iShouldBeNotifiedThatIMustEnterMyEmail()
    {
        Assert::same(
            $this->createPage->getAuthorValidationMessage(),
            'Please enter your email.',
            'There should be author validation error, but there is not.'
        );
    }

    /**
     * @Then I should be notified that this email is already registered
     */
    public function iShouldBeNotifiedThatThisEmailIsAlreadyRegistered()
    {
        Assert::same(
            $this->createPage->getAuthorValidationMessage(),
            'This email is already registered, please login or use forgotten password.',
            'There should be author validation error, but there is not.'
        );
    }

    /**
     * @return string
     */
    private function getVeryLongTitle()
    {
        return 'Exegi monumentum aere perennius regalique situ pyramidum altius, quod non imber edax, non Aquilo inpotens possit diruere aut innumerabilis annorum series et fuga temporum. Non omnis moriar multaque pars mei vitabit Libitinam; usque ego postera crescam laude recens, dum Capitoliumscandet cum tacita virgine pontifex.Dicar, qua violens obstrepit Aufiduset qua pauper aquae Daunus agrestiumregnavit populorum, ex humili potensprinceps Aeolium carmen ad Italosdeduxisse modos. Sume superbiamquaesitam meritis et mihi Delphicalauro cinge volens, Melpomene, comam.';
    }
}
