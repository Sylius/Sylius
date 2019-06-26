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
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Behat\Page\Admin\Payment\IndexPageInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

final class ManagingPaymentsContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var ShowPageInterface */
    private $orderShowPage;

    public function __construct(IndexPageInterface $indexPage, ShowPageInterface $orderShowPage)
    {
        $this->indexPage = $indexPage;
        $this->orderShowPage = $orderShowPage;
    }

    /**
     * @When I browse payments
     */
    public function iBrowsePayments(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :count payments in the list
     */
    public function iShouldSeePaymentsInTheList(int $count): void
    {
        Assert::same($count, $this->indexPage->countItems());
    }

    /**
     * @Then the payments of the :orderNumber order should be :paymentState for :customer
     */
    public function thePaymentsOfTheOrderShouldBeFor(
        string $orderNumber,
        string $paymentState,
        CustomerInterface $customer,
        Channel $channel = null
    ): void {
        $parameters = [
            'number' => $orderNumber,
            'state' => $paymentState,
            'customer' => $customer->getEmail(),
        ];

        if ($channel !== null) {
            $parameters = ['channel' => $channel->getCode()];
        }

        Assert::true($this->indexPage->isSingleResourceOnPage($parameters));
    }
}
