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

use Sylius\Behat\Page\SyliusPage;

final class ResendVerificationEmailPage extends SyliusPage implements ResendVerificationEmailPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_resend_verification_email';
    }

    public function resend(): void
    {
        $this->getElement('resend_button')->click();
    }

    public function specifyEmail(?string $email): void
    {
        $this->getElement('email')->setValue($email);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '[data-test-resend-verification-email]',
            'resend_button' => '[data-test-resend-verification-email-button]',
        ]);
    }
}
