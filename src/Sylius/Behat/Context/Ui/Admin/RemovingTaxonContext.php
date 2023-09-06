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
use Sylius\Behat\Page\Admin\Taxon\CreatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class RemovingTaxonContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private SharedStorageInterface $sharedStorage,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @When I remove taxon named :taxon
     * @When I delete taxon named :taxon
     * @When I try to delete taxon named :taxon
     */
    public function iRemoveTaxonNamed(TaxonInterface $taxon): void
    {
        $this->createPage->open();
        $this->createPage->deleteTaxonOnPageByName($taxon->getName());
        $this->sharedStorage->set('taxon', $taxon);
    }

    /**
     * @Then /^(this taxon) should still exist$/
     */
    public function theTaxonShouldStillExist(TaxonInterface $taxon): void
    {
        $this->createPage->open();

        Assert::true($this->createPage->hasTaxonWithName($taxon->getName()));
    }

    /**
     * @Then I should be notified that this taxon could not be deleted as it is in use by a promotion rule
     */
    public function iShouldBeNotifiedThatThisTaxonCouldNotBeDeleted(): void
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete a taxon that is in use by a promotion rule.',
            NotificationType::failure(),
        );
    }
}
