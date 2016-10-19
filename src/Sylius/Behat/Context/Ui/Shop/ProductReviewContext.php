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
     */
    public function iLeaveACommentTitled($comment, $title)
    {
        $this->createPage->titleReview($title);
        $this->createPage->setComment($comment);
    }

    /**
     * @Given I rate it with :rate points
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
}
