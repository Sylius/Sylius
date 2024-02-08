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

namespace Sylius\Behat\Page\Shop\Account;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class DashboardPage extends SymfonyPage implements DashboardPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_account_dashboard';
    }

    public function hasCustomerName(string $name): bool
    {
        return $this->hasValueInCustomerSection($name);
    }

    public function hasCustomerEmail(string $email): bool
    {
        return $this->hasValueInCustomerSection($email);
    }

    public function isVerified(): bool
    {
        return !$this->hasElement('verification');
    }

    public function hasResendVerificationEmailButton(): bool
    {
        return $this->hasElement('verification_button');
    }

    public function pressResendVerificationEmail(): void
    {
        $this->getElement('verification_button')->press();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer' => '[data-test-customer-information]',
            'verification' => '[data-test-verification-form]',
            'verification_button' => '[data-test-verification-button]',
        ]);
    }

    private function hasValueInCustomerSection(string $value): bool
    {
        $customerText = $this->getElement('customer')->getText();

        return stripos($customerText, $value) !== false;
    }
}
