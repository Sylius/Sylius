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
     * @When I leave a comment :comment, titled :title
     * @When I leave a comment :comment, titled :title as :author
     * @When I leave a comment :comment as :author
     */
    public function iLeaveACommentTitled($comment, $title = null, $author = null)
    {
        $this->createPage->titleReview($title);
        $this->createPage->setComment($comment);

        if (null !== $author) {
            $this->createPage->setAuthor($author);
        }
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
        Assert::true(
            $this->createPage->hasRateValidationMessage(),
            'There should be rate validation error, but there is not.'
        );
    }

    /**
     * @Then I should be notified that I title is required
     */
    public function iShouldBeNotifiedThatTitleIsRequired()
    {
        Assert::true(
            $this->createPage->hasTitleValidationMessage(),
            'There should be title validation error, but there is not.'
        );
    }
}
