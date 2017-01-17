<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class AddressFactory implements AddressFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @param FactoryInterface $decoratedFactory
     */
    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createForCustomer(CustomerInterface $customer)
    {
        /** @var  AddressInterface $address*/
        $address = $this->decoratedFactory->createNew();
        $address->setCustomer($customer);
        
        return $address;
    }
}
