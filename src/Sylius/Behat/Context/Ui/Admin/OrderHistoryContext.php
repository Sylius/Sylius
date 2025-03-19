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
use Sylius\Behat\Page\Admin\Order\HistoryPageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class OrderHistoryContext implements Context
{
    public function __construct(
        private HistoryPageInterface $historyPage,
    ) {
    }

    /**
     * @When I browse order's :order history
     */
    public function iBrowseOrderHistory(OrderInterface $order): void
    {
        $this->historyPage->open(['id' => $order->getId()]);
    }

    /**
     * @Then there should be :count shipping address changes in the registry
     */
    public function thereShouldBeCountShippingAddressChangesInTheRegistry(int $count): void
    {
        Assert::same($this->historyPage->countShippingAddressChanges(), $count);
    }

    /**
     * @Then there should be :count billing address changes in the registry
     */
    public function thereShouldBeCountBillingAddressChangesInTheRegistry(int $count): void
    {
        Assert::same($this->historyPage->countBillingAddressChanges(), $count);
    }
}
