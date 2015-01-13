<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

/**
 * @author Matthias Esterl <inventor@madcity.at>
 */
interface SortableInterface
{
    /**
     * Get position of the item.
     *
     * @return integer
     */
    public function getPosition();

    /**
     * Set position of the item.
     *
     * @param integer $position
     */
    public function setPosition($position);
}
