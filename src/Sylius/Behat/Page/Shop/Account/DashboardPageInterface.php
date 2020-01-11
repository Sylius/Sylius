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
    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasCustomerName($name);

    /**
     * @param string $email
     *
     * @return bool
     */
    public function hasCustomerEmail($email);

    /**
     * @return bool
     */
    public function isVerified();

    /**
     * @return bool
     */
    public function hasResendVerificationEmailButton();

    public function pressResendVerificationEmail();
}
