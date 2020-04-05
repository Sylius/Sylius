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
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class EmailContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var EmailCheckerInterface */
    private $emailChecker;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        EmailCheckerInterface $emailChecker,
        TranslatorInterface $translator
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->emailChecker = $emailChecker;
        $this->translator = $translator;
    }

    /**
     * @Then it should be sent to :recipient
     * @Then the email with contact request should be sent to :recipient
     */
    public function anEmailShouldBeSentTo(string $recipient): void
    {
        Assert::true($this->emailChecker->hasRecipient($recipient));
    }

    /**
     * @Then an email with reset token should be sent to :recipient
     * @Then an email with reset token should be sent to :recipient in :localeCode locale
     */
    public function anEmailWithResetTokenShouldBeSentTo(string $recipient, string $localeCode = 'en_US'): void
    {
        $this->assertEmailContainsMessageTo(
            $this->translator->trans('sylius.email.password_reset.reset_your_password', [], null, $localeCode),
            $recipient
        );
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
     * @Then a welcoming email should have been sent to :recipient in :localeCode locale
     */
    public function aWelcomingEmailShouldHaveBeenSentTo(string $recipient, string $localeCode = 'en_US'): void
    {
        $this->assertEmailContainsMessageTo(
            $this->translator->trans('sylius.email.user_registration.welcome_to_our_store', [], null, $localeCode),
            $recipient
        );
    }

    /**
     * @Then an email with the confirmation of the order :order should be sent to :email
     * @Then an email with the confirmation of the order :order should be sent to :email in :localeCode locale
     */
    public function anEmailWithTheConfirmationOfTheOrderShouldBeSentTo(
        OrderInterface $order,
        string $recipient,
        string $localeCode = 'en_US'
    ): void {
        $this->assertEmailContainsMessageTo(
            sprintf(
                '%s %s %s',
                $this->translator->trans('sylius.email.order_confirmation.your_order_number', [], null, $localeCode),
                $order->getNumber(),
                $this->translator->trans('sylius.email.order_confirmation.has_been_successfully_placed', [], null, $localeCode)
            ),
            $recipient
        );
    }

    /**
     * @Then /^an email with the summary of (order placed by "[^"]+") should be sent to him$/
     * @Then /^an email with the summary of (order placed by "[^"]+") should be sent to him in ("([^"]+)" locale)$/
     */
    public function anEmailWithSummaryOfOrderPlacedByShouldBeSentTo(OrderInterface $order, string $localeCode = 'en_US'): void
    {
        $this->anEmailWithTheConfirmationOfTheOrderShouldBeSentTo($order, $order->getCustomer()->getEmailCanonical(), $localeCode);
    }

    /**
     * @Then /^an email with shipment's details of (this order) should be sent to "([^"]+)"$/
     * @Then /^an email with shipment's details of (this order) should be sent to "([^"]+)" in ("([^"]+)" locale)$/
     * @Then an email with the shipment's confirmation of the order :order should be sent to :recipient
     * @Then an email with the shipment's confirmation of the order :order should be sent to :recipient in :localeCode locale
     */
    public function anEmailWithShipmentDetailsOfOrderShouldBeSentTo(
        OrderInterface $order,
        string $recipient,
        string $localeCode = 'en_US'
    ): void {
        $this->assertEmailContainsMessageTo(
            sprintf(
                '%s %s %s %s.',
                $this->translator->trans('sylius.email.shipment_confirmation.your_order_with_number', [], null, $localeCode),
                $order->getNumber(),
                $this->translator->trans('sylius.email.shipment_confirmation.has_been_sent_using', [], null, $localeCode),
                $this->getShippingMethodName($order)
            ),
            $recipient
        );

        if ($this->sharedStorage->has('tracking_code')) {
            $this->assertEmailContainsMessageTo(
                sprintf(
                    '%s %s %s',
                    $this->sharedStorage->get('tracking_code'),
                    $this->translator->trans('sylius.email.shipment_confirmation.tracking_code', [], null, $localeCode),
                    $this->translator->trans('sylius.email.shipment_confirmation.thank_you_for_transaction', [], null, $localeCode)
                ),
                $recipient
            );
        }
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
