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

/**
 * Filter address event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterAddressEvent extends Event
{
    /**
     * Address.
     *
     * @var AddressInterface
     */
    private $address;

    /**
     * Constructor.
     *
     * @param AddressInterface $address
     */
    public function __construct(AddressInterface $address)
    {
        $this->address = $address;
    }

    /**
     * Get address.
     *
     * @return AddressInterface
     */
    public function getAddress()
    {
        return $this->address;
    }
}
