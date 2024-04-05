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

namespace Sylius\Behat\Page\Admin\Customer;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function deleteAccount(): void
    {
        $this->getElement('delete_account_button')->press();
    }

    public function getCustomerEmail(): string
    {
        return $this->getElement('customer_email')->getText();
    }

    public function getCustomerPhoneNumber(): string
    {
        return $this->getElement('customer_phone_number')->getText();
    }

    public function getCustomerName(): string
    {
        return $this->getElement('customer_name')->getText();
    }

    public function getRegistrationDate(): \DateTimeInterface
    {
        return \DateTime::createFromFormat('d-m-Y H:i:s', $this->getElement('registration_date')->getText());
    }

    public function getDefaultAddress(): string
    {
        return $this->getElement('default_address')->getText();
    }

    public function hasAccount(): bool
    {
        return $this->hasElement('delete_account_button');
    }

    public function isSubscribedToNewsletter(): bool
    {
        return null !== $this->getElement('subscribed_to_newsletter')->find('css', 'svg.text-green');
    }

    public function hasDefaultAddressProvinceName(string $provinceName): bool
    {
        $defaultAddressProvince = $this->getElement('default_address')->getText();

        return false !== stripos($defaultAddressProvince, $provinceName);
    }

    public function hasVerifiedEmail(): bool
    {
        return null !== $this->getElement('verified_email')->find('css', 'svg.text-green');
    }

    public function getGroupName(): string
    {
        return $this->getElement('group')->getText();
    }

    public function hasEmailVerificationInformation(): bool
    {
        return null === $this->getDocument()->find('css', '#verified-email');
    }

    public function hasImpersonateButton(): bool
    {
        return $this->hasElement('impersonate_button');
    }

    public function impersonate(): void
    {
        $this->getElement('impersonate_button')->click();
    }

    public function hasCustomerPlacedAnyOrders(): bool
    {
        return !$this->hasElement('statistics_no_orders');
    }

    public function getOrdersCountInChannel(string $channelCode): int
    {
        return (int) $this->getElement('statistics_orders_count', ['%channelCode%' => $channelCode])->getText();
    }

    public function getOrdersTotalInChannel(string $channelCode): string
    {
        return $this->getElement('statistics_orders_total', ['%channelCode%' => $channelCode])->getText();
    }

    public function getAverageTotalInChannel(string $channelCode): string
    {
        return $this->getElement('statistics_orders_average', ['%channelCode%' => $channelCode])->getText();
    }

    public function getSuccessFlashMessage(): string
    {
        return trim($this->getElement('flash_message')->getText());
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_customer_show';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer_email' => '[data-test-customer-email]',
            'customer_name' => '[data-test-customer-fullname]',
            'customer_phone_number' => '[data-test-customer-phone]',
            'default_address' => '[data-test-customer-default-address]',
            'delete_account_button' => '[data-test-customer-actions-delete]',
            'group' => '[data-test-customer-group]',
            'impersonate_button' => '[data-test-customer-actions-impersonate]',
            'registration_date' => '[data-test-customer-since]',
            'statistics_no_orders' => '[data-test-customer-statistics-no-orders]',
            'statistics_orders_average' => '[data-test-customer-statistics-%channelCode%-orders-average]',
            'statistics_orders_count' => '[data-test-customer-statistics-%channelCode%-orders-count]',
            'statistics_orders_total' => '[data-test-customer-statistics-%channelCode%-orders-total]',
            'subscribed_to_newsletter' => '[data-test-customer-subscribed-to-newsletter]',
            'verified_email' => '[data-test-customer-verified-email]',
        ]);
    }
}
