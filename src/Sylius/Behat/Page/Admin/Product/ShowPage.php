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
use Sylius\Component\Core\Model\ChannelInterface;

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

    public function specifyChannel(ChannelInterface $channel): void
    {
        $this->getElement('scrolling_menu')->find('css', sprintf("a:contains('%s')", $channel->getName()))->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'product_name' => '#header h1 .content > span',
            'scrolling_menu' => '.scrolling.menu',
            'variants' => '#variants',
        ]);
    }
}
