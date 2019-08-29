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

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Webmozart\Assert\Assert;

final class EmailContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var EmailCheckerInterface */
    private $emailChecker;

    public function __construct(SharedStorageInterface $sharedStorage, EmailCheckerInterface $emailChecker)
    {
        $this->sharedStorage = $sharedStorage;
        $this->emailChecker = $emailChecker;
    }

    /**
     * @Then it should be sent to :recipient
     * @Then the email with reset token should be sent to :recipient
     * @Then the email with contact request should be sent to :recipient
     */
    public function anEmailShouldBeSentTo(string $recipient): void
    {
        Assert::true($this->emailChecker->hasRecipient($recipient));
    }

    /**
     * @Then an email with the :method shipment's confirmation for the :orderNumber order should be sent to :email
     */
    public function anEmailWithShipmentsConfirmationForTheOrderShouldBeSentTo(string $method, string $orderNumber, string $recipient): void
    {
        Assert::true($this->emailChecker->hasMessageTo(
            sprintf(
                'Your order with number %s has been sent using %s.',
                $orderNumber,
                $method
            ),
            $recipient
        ));
    }

    /**
     * @Then :count email(s) should be sent to :recipient
     */
    public function numberOfEmailsShouldBeSentTo(int $count, string $recipient): void
    {
        Assert::same($this->emailChecker->countMessagesTo($recipient), $count);
    }

    /**
     * @Then a welcoming email should have been sent to :recipient
     */
    public function aWelcomingEmailShouldHaveBeenSentTo(string $recipient): void
    {
        $this->assertEmailContainsMessageTo('Welcome to our store', $recipient);
    }

    /**
     * @Then /^an email with the summary of (order placed by "([^"]+)") should be sent to him$/
     */
    public function anEmailWithOrderConfirmationShouldBeSentTo(OrderInterface $order): void
    {
        $this->assertEmailContainsMessageTo(
            sprintf(
                'Your order no. %s has been successfully placed.',
                $order->getNumber()
            ),
            $order->getCustomer()->getEmailCanonical()
        );
    }

    /**
     * @Then /^an email with shipment's details of (this order) should be sent to "([^"]+)"$/
     */
    public function anEmailWithShipmentDetailsOfOrderShouldBeSentTo(OrderInterface $order, string $recipient): void
    {
        $this->assertEmailContainsMessageTo($order->getNumber(), $recipient);
        $this->assertEmailContainsMessageTo($this->getShippingMethodName($order), $recipient);

        $tracking = $this->sharedStorage->get('tracking_code');
        $this->assertEmailContainsMessageTo($tracking, $recipient);
    }

    private function assertEmailContainsMessageTo(string $message, string $recipient): void
    {
        Assert::true($this->emailChecker->hasMessageTo($message, $recipient));
    }

    private function getShippingMethodName(OrderInterface $order): string
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $order->getShipments()->first();
        if (false === $shipment) {
            throw new \LogicException('Order should have at least one shipment.');
        }

        return $shipment->getMethod()->getName();
    }
}
