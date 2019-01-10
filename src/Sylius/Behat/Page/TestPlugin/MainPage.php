<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\TestPlugin;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class MainPage extends SymfonyPage implements MainPageInterface
{
    public function getContent(): string
    {
        return $this->getDocument()->find('css', 'body')->getText();
    }

    public function getRouteName(): string
    {
        return 'sylius_test_plugin_main';
    }
}
