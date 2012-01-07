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

use Sylius\Bundle\AddressingBundle\Model\AddressManagerInterface;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;

/**
 * Address manipulator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class AddressManipulator implements AddressManipulatorInterface
{
    /**
     * Address manager.
     *
     * @var AddressManagerInterface
     */
    protected $addressManager;

    /**
     * Constructor.
     *
     * @param $addressManager AddressManagerInterface
     */
    public function __construct(AddressManagerInterface $addressManager)
    {
        $this->addressManager = $addressManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(AddressInterface $address)
    {
        $address->incrementCreatedAt();
        $this->addressManager->persistAddress($address);
    }

  /**
     * {@inheritdoc}
     */
    public function update(AddressInterface $address)
    {
        $address->incrementUpdatedAt();
        $this->addressManager->persistAddress($address);
    }

  /**
     * {@inheritdoc}
     */
    public function delete(AddressInterface $address)
    {
        $this->addressManager->removeAddress($address);
    }
}
