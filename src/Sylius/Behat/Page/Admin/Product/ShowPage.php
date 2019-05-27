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

namespace Sylius\Behat\Page\Admin\Product;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function isSimpleProductPage(): bool
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
            'product_name' => '#header h1 .content > span',
            'variants' => '#variants',
        ]);
    }
}
