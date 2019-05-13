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

namespace Sylius\Bundle\ProductBundle\Tests;

use PHPUnit\Framework\Assert;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAssociationTypeRepository;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionRepository;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class SyliusProductBundleTest extends WebTestCase
{
    /**
     * @test
     */
    public function its_services_are_initializable(): void
    {
        /** @var ContainerBuilder $container */
        $container = self::createClient()->getContainer();

        $services = $container->getServiceIds();

        $services = array_filter($services, function (string $serviceId): bool {
            return 0 === strpos($serviceId, 'sylius.');
        });

        foreach ($services as $id) {
            Assert::assertNotNull($container->get($id, ContainerInterface::NULL_ON_INVALID_REFERENCE));
        }
    }

    /**
     * @test
     */
    public function its_initializes_default_doctrine_repositories(): void
    {
        /** @var ContainerBuilder $container */
        $container = self::createClient()->getContainer();

        $repositories = [
            'sylius.repository.product' => ProductRepository::class,
            'sylius.repository.product_association_type' => ProductAssociationTypeRepository::class,
            'sylius.repository.product_variant' => ProductVariantRepository::class,
            'sylius.repository.product_attribute' => EntityRepository::class,
            'sylius.repository.product_option' => ProductOptionRepository::class,
        ];

        foreach ($repositories as $id => $class) {
            $service = $container->get($id, ContainerInterface::NULL_ON_INVALID_REFERENCE);
            Assert::assertEquals(get_class($service), $repositories[$id]);
        }
    }
}
