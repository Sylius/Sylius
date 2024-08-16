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
use Sylius\Behat\Element\Admin\Taxon\TreeElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final readonly class RemovingTaxonContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private TreeElementInterface $treeElement,
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
        $this->treeElement->deleteTaxon($taxon->getName());
    }

    /**
     * @Then the :taxonName taxon should still exist
     */
    public function theTaxonShouldStillExist(string $taxonName): void
    {
        $this->createPage->open();

        Assert::true($this->treeElement->isTaxonOnTheList($taxonName));
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
