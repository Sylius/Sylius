<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Tests\CommandHandler;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\AddressOrderHandler;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ChangeAddressOrderHandlerTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_changes_address_order_without_duplication_in_database(): void
    {
        $container = self::bootKernel()->getContainer();

        /** @var RepositoryInterface $addressRepository */
        $addressRepository = $container->get('sylius.repository.address');

        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $container->get('sylius.repository.customer');

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $container->get('sylius.repository.order');

        /** @var FactoryInterface $customerFactory */
        $customerFactory =  $container->get('sylius.factory.customer');

        /** @var ObjectManager $manager */
        $manager = $container->get('doctrine.orm.default_entity_manager');

        /** @var StateMachineFactoryInterface $stateMachineFactory */
        $stateMachineFactory = $container->get('sm.factory');

        $purger = new ORMPurger($manager);
        $purger->purge();

        /** @var LoaderInterface $loader */
        $loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');

        $loader->load(
            [
                'Tests/config/fixtures/address.yaml',
                'Tests/config/fixtures/channel.yaml',
                'Tests/config/fixtures/currency.yaml',
                'Tests/config/fixtures/locale.yaml',
                'Tests/config/fixtures/order.yaml',
            ],
            [],
            [],
            PurgeMode::createDeleteMode()
        );

        /** @var AddressMapperInterface $addressMapper */
        $addressMapper = $container->get('Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface');

        $addressOrderHandler = new AddressOrderHandler(
            $orderRepository,
            $customerRepository,
            $customerFactory,
            $manager,
            $stateMachineFactory,
            $addressMapper
        );

        $newBillingAddress = $address = new Address();
        $address->setFirstName('John');
        $address->setLastName('Bidd');
        $address->setCompany('CocaCola');
        $address->setStreet('Green Avenue');
        $address->setCountryCode('US');
        $address->setCity('Washington');
        $address->setPostcode('11111');
        $address->setProvinceCode('100100100');
        $address->setProvinceName('111');
        $address->setPhoneNumber('west');

        $addressOrder = new AddressOrder('john@doe.com', $newBillingAddress);
        $addressOrder->setOrderTokenValue('token');

        $addressOrderHandler($addressOrder);

        $this->assertSame(1, count($addressRepository->findAll()));
    }
}
