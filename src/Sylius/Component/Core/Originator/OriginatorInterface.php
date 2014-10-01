<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Originator;

use Sylius\Component\Core\Model\OriginAwareInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface OriginatorInterface
{
    public function getOrigin(OriginAwareInterface $originAware);
    public function setOrigin(OriginAwareInterface $originAware, $origin);
}
