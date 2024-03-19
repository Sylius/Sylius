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

final class RegisterThankYouPage extends SymfonyPage implements RegisterThankYouPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_register_thank_you';
    }
}
