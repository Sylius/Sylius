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

namespace Tests\Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ShippingMethodRepositoryTest extends KernelTestCase
{
    /** @var ShippingMethodRepositoryInterface<ShippingMethodInterface> */
    private ShippingMethodRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();

        /** @var ShippingMethodRepositoryInterface<ShippingMethodInterface> $repository */
        $repository = self::getContainer()->get('sylius.repository.shipping_method');
        $this->repository = $repository;
    }

    #[Test]
    public function it_finds_shipping_methods_by_channel_code_in_configuration(): void
    {
        $shippingMethods = $this->repository->findByChannelCodeInConfiguration('WEB_US');

        self::assertCount(2, $shippingMethods);
        self::assertEqualsCanonicalizing(
            ['dhl_express', 'ups'],
            $this->getShippingMethodCodes($shippingMethods),
        );
    }

    #[Test]
    public function it_returns_empty_array_when_channel_code_not_found(): void
    {
        $shippingMethods = $this->repository->findByChannelCodeInConfiguration('NON_EXISTENT_CHANNEL');

        self::assertCount(0, $shippingMethods);
    }

    #[Test]
    public function it_finds_multiple_shipping_methods_with_same_channel_code(): void
    {
        $shippingMethods = $this->repository->findByChannelCodeInConfiguration('WEB_EU');

        self::assertCount(2, $shippingMethods);
        self::assertEqualsCanonicalizing(
            ['dhl_express', 'fedex'],
            $this->getShippingMethodCodes($shippingMethods),
        );
    }

    /**
     * @param ShippingMethodInterface[] $shippingMethods
     *
     * @return string[]
     */
    private function getShippingMethodCodes(array $shippingMethods): array
    {
        return array_map(
            static fn (ShippingMethodInterface $method): string => $method->getCode(),
            $shippingMethods,
        );
    }

    private function loadFixtures(): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()->get('doctrine.orm.default_entity_manager');

        (new ORMPurger($manager))->purge();

        $fixtureLoader->load([
            __DIR__ . '/ShippingMethodRepositoryTest/fixtures.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
