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
     * @return int
     */
    public function getOriginCode();

    /**
     * @param int $originCode
     */
    public function setOriginCode($originCode);
}
