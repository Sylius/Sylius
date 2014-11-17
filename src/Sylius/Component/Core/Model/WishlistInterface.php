<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;

interface WishlistInterface extends UserAwareInterface
{
    /**
     * @return Collection|WishlistItemInterface[]
     */
    public function getItems();

    /**
     * @param WishlistItemInterface $item
     */
    public function addItem(WishlistItemInterface $item);

    /**
     * @param WishlistItemInterface $item
     */
    public function removeItem(WishlistItemInterface $item);
}
