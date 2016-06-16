<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShippingMethodFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @var FactoryInterface
     */
    private $shippingMethodFactory;

    /**
     * @var ObjectManager
     */
    private $shippingMethodManager;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->shippingMethodFactory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $this->shippingMethodManager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $this->zoneRepository = $this->getMockBuilder(RepositoryInterface::class)->getMock();
        $this->shippingCategoryRepository = $this->getMockBuilder(RepositoryInterface::class)->getMock();
        $this->localeRepository = $this->getMockBuilder(RepositoryInterface::class)->getMock();

        $this->zoneRepository->method('findOneBy')->willReturn($this->getMock(ZoneInterface::class));
        $this->shippingCategoryRepository->method('findOneBy')->willReturn($this->getMock(ShippingCategoryInterface::class));
    }

    /**
     * @test
     */
    public function it_requires_shipping_methods_node_to_be_set()
    {
        $this->assertPartialConfigurationIsInvalid([[]], 'shipping_methods');
        $this->assertPartialConfigurationIsInvalid([['shipping_methods' => null]], 'shipping_methods');
        $this->assertPartialConfigurationIsInvalid([['shipping_methods' => []]], 'shipping_methods');
    }

    /**
     * @test
     */
    public function it_generates_random_shipping_methods_names_if_number_is_given()
    {
        $processedConfiguration = (new PartialProcessor())->processConfiguration(
            $this->getConfiguration(),
            'shipping_methods',
            [['shipping_methods' => 3]]
        );

        static::assertCount(3, $processedConfiguration['shipping_methods']);

        $processedConfiguration = (new PartialProcessor())->processConfiguration(
            $this->getConfiguration(),
            'shipping_methods',
            [['shipping_methods' => '2']]
        );

        static::assertCount(2, $processedConfiguration['shipping_methods']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ShippingMethodFixture(
            $this->shippingMethodFactory,
            $this->shippingMethodManager,
            $this->zoneRepository,
            $this->shippingCategoryRepository,
            $this->localeRepository
        );
    }
}
