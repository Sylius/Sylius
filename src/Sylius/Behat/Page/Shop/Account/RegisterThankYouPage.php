<?php
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
