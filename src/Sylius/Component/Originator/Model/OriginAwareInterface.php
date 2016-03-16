<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Originator\Model;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface OriginAwareInterface
{
    /**
     * Return origin identifier.
     *
     * @return int
     */
    public function getOriginId();

    /**
     * Set origin identifier.
     *
     * @param int $originId
     */
    public function setOriginId($originId);

    /**
     * Return origin type info.
     *
     * @return string
     */
    public function getOriginType();

    /**
     * Set origin type info.
     *
     * @param string $originType
     */
    public function setOriginType($originType);
}
