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

use Sylius\Behat\Page\PageInterface;

interface DashboardPageInterface extends PageInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasCustomerName(string $name): bool;

    /**
     * @param string $email
     *
     * @return bool
     */
    public function hasCustomerEmail(string $email): bool;

    /**
     * @return bool
     */
    public function isVerified(): bool;

    /**
     * @return bool
     */
    public function hasResendVerificationEmailButton(): bool;

    public function pressResendVerificationEmail(): void;
}
