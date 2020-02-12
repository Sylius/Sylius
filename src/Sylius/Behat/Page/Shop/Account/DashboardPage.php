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
        return $this->getDocument()->hasButton('Verify');
    }

    public function pressResendVerificationEmail(): void
    {
        $this->getDocument()->pressButton('Verify');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer' => '#customer-information',
            'verification' => '#verification-form',
        ]);
    }

    private function hasValueInCustomerSection(string $value): bool
    {
        $customerText = $this->getElement('customer')->getText();

        return stripos($customerText, $value) !== false;
    }
}
