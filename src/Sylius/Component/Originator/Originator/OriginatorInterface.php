<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Originator\Originator;

use Sylius\Component\Originator\Model\OriginAwareInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface OriginatorInterface
{
    /**
     * @param OriginAwareInterface $originAware
     *
     * @return null|object
     */
    public function getOrigin(OriginAwareInterface $originAware);

    /**
     * @param OriginAwareInterface $originAware
     * @param object               $origin
     */
    public function setOrigin(OriginAwareInterface $originAware, $origin);
}
