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

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface DashboardPageInterface extends PageInterface
{
    public function hasCustomerName(string $name): bool;

    public function hasCustomerEmail(string $email): bool;

    public function isVerified(): bool;

    public function hasResendVerificationEmailButton(): bool;

    public function pressResendVerificationEmail(): void;
}
