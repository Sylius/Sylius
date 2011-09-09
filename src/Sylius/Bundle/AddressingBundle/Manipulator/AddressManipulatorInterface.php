<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Manipulator;

use Sylius\Bundle\AddressingBundle\Model\AddressInterface;

/**
 * Address manipulator interface.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface AddressManipulatorInterface
{
    /**
     * Creates a address.
     * 
     * @param AddressInterface $address
     */
    function create(AddressInterface $address);

    /**
     * Updates a address.
     * 
     * @param AddressInterface $address
     */
    function update(AddressInterface $address);
    
    /**
     * Deletes a address.
     * 
     * @param AddressInterface $address
     */
    function delete(AddressInterface $address);
}
