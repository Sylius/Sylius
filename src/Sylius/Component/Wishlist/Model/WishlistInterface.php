<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Wishlist\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Originator\Model\OriginAwareInterface;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface WishlistInterface extends SlugAwareInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return bool
     */
    public function isPublic();

    /**
     * @param bool $public
     */
    public function setPublic($public);

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
