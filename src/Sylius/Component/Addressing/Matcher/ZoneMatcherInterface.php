<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Matcher;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ZoneMatcherInterface
{
    /**
     * @param AddressInterface $address
     * @param string|null      $scope
     *
     * @return ZoneInterface|null
     */
    public function match(AddressInterface $address, $scope = null);

    /**
     * @param AddressInterface $address
     * @param string|null      $scope
     *
     * @return Collection|ZoneInterface[]
     */
    public function matchAll(AddressInterface $address, $scope = null);
}
