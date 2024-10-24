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
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class RemovingProductContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private IndexPageInterface $indexPage,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @When I delete the :product product
     * @When I try to delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product): void
    {
        $this->sharedStorage->set('product', $product);

        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $product->getName()]);
    }

    /**
     * @When I delete the :product product on filtered page
     */
    public function iDeleteProductOnFilteredPage(ProductInterface $product): void
    {
        $this->sharedStorage->set('product', $product);

        $this->indexPage->deleteResourceOnPage(['name' => $product->getName()]);
    }

    /**
     * @Then /^(this product) should still exist$/
     */
    public function theProductShouldStillExist(ProductInterface $product): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $product->getName()]));
    }

    /**
     * @Then I should be notified that this product could not be deleted as it is in use by a promotion rule
     */
    public function iShouldBeNotifiedThatThisProductCouldNotBeDeleted(): void
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete a product that is in use by a promotion rule.',
            NotificationType::failure(),
        );
    }
}
