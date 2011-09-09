<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\EventDispatcher\Event;

use Sylius\Bundle\AddressingBundle\Model\AddressInterface;

use Symfony\Component\EventDispatcher\Event;

class FilterAddressEvent extends Event
{
    private $address;
    
    public function __construct(AddressInterface $address)
    {
        $this->address = $address;
    }
    
    public function getAddress()
    {
        return $this->address;
    }
}
