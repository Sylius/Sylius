<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Product;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function itIsSimpleProductPage(): bool
    {
       return !$this->hasElement('variants');
    }

    public function getName(): string
    {
        return $this->getElement('product_name')->getText();
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_product_show';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'variants' => '#variants',
            'product_name' => '#header h1 .content > span',
        ]);
    }
}
