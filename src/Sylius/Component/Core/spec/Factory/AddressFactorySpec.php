<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\AddressFactory;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class AddressFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory)
    {
        $this->beConstructedWith($decoratedFactory);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType(AddressFactory::class);
    }
    
    function it_implements_address_factory_interface()
    {
        $this->shouldImplement(AddressFactoryInterface::class);
    }
    
    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }
    
    function it_creates_a_new_address(FactoryInterface $decoratedFactory, AddressInterface $address)
    {
        $decoratedFactory->createNew()->willReturn($address);
        
        $this->createNew()->shouldReturn($address);
    }
    
    function it_creates_a_new_address_with_customer(
        FactoryInterface $decoratedFactory, 
        AddressInterface $address, 
        CustomerInterface $customer
    ) {
        $decoratedFactory->createNew()->willReturn($address);
        
        $address->setCustomer($customer)->shouldBeCalled();

        $this->createForCustomer($customer)->shouldReturn($address);
    }
}
