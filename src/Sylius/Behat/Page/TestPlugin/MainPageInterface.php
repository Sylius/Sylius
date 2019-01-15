<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\TestPlugin;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface MainPageInterface extends SymfonyPageInterface
{
    public function getContent(): string;
}
