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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function getName(): string;

    public function isSimpleProductPage(): bool;

    public function specifyChannel(ChannelInterface $channel): void;
}
