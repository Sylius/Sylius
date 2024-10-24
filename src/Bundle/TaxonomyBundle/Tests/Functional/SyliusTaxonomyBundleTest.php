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

namespace Sylius\Bundle\TaxonomyBundle\Tests\Functional;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class SyliusTaxonomyBundleTest extends KernelTestCase
{
    /**
     * @test
     */
    public function its_services_are_initializable()
    {
        static::bootKernel();

        /** @var Container $container */
        $container = self::$kernel->getContainer();

        $serviceIds = array_filter($container->getServiceIds(), fn (string $serviceId): bool => str_starts_with($serviceId, 'sylius.'));

        foreach ($serviceIds as $id) {
            Assert::assertNotNull($container->get($id, ContainerInterface::NULL_ON_INVALID_REFERENCE));
        }
    }
}
