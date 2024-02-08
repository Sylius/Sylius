<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
use Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\UpdateCartHandler;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
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

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $container->get('sylius.repository.order');

        /** @var ObjectManager $manager */
        $manager = $container->get('doctrine.orm.default_entity_manager');

        $orderAddressModifier = $container->get(OrderAddressModifierInterface::class);

        $orderPromotionCodeAssigner = $container->get(OrderPromotionCodeAssignerInterface::class);

        $customerResolver = $container->get(CustomerResolverInterface::class);

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
            PurgeMode::createDeleteMode(),
        );

        $updateCartHandler = new UpdateCartHandler(
            $orderRepository,
            $orderAddressModifier,
            $orderPromotionCodeAssigner,
            $customerResolver,
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

        $updateCart = new UpdateCart('john@doe.com', $newBillingAddress);
        $updateCart->setOrderTokenValue('token');

        $updateCartHandler($updateCart);

        $this->assertCount(1, $addressRepository->findAll());
    }
}
