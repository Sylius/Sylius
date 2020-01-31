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

namespace Sylius\Behat\Page\Admin\Shipment;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface ShowPageInterface extends PageInterface
{
    public function hasShipmentUnit(ProductInterface $product): bool;
}
