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

class VerificationPage extends SymfonyPage implements VerificationPageInterface
{
    public function verifyAccount(string $token): void
    {
        $this->tryToOpen(['token' => $token]);
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_user_verification';
    }
}
