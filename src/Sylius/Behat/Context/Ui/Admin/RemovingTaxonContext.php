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
use Sylius\Behat\Page\Admin\Taxon\CreatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\PromotionInterface;

final class RemovingTaxonContext implements Context
{
    /** @var CreatePageInterface */
    private $createPage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(CreatePageInterface $createPage, NotificationCheckerInterface $notificationChecker)
    {
        $this->createPage = $createPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I remove taxon named :name
     * @When I delete taxon named :name
     * @When I try to delete taxon named :name
     */
    public function iRemoveTaxonNamed(string $name): void
    {
        $this->createPage->open();
        $this->createPage->deleteTaxonOnPageByName($name);
    }

    /**
     * @Then I should be notified that :promotion promotion has been updated
     */
    public function iShouldBeNotifiedThatPromotionHasBeenUpdated(PromotionInterface $promotion): void
    {
        $this->notificationChecker->checkNotification(
            sprintf('Some rules of the promotions with codes %s have been updated.', $promotion->getCode()),
            NotificationType::info()
        );
    }
}
