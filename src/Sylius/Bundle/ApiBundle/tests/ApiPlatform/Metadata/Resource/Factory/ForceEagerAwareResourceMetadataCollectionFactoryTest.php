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

namespace Tests\Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory\ForceEagerAwareResourceMetadataCollectionFactory;

#[CoversClass(ForceEagerAwareResourceMetadataCollectionFactory::class)]
final class ForceEagerAwareResourceMetadataCollectionFactoryTest extends TestCase
{
    #[Test]
    #[TestWith([false])]
    #[TestWith([true])]
    public function it_sets_configured_force_eager_on_sylius_read_operations_without_explicit_value(bool $forceEager): void
    {
        $operations = $this->getOperations($this->createFactory($forceEager));

        $this->assertSame($forceEager, $operations['sylius_api_admin_product_get_collection']->getForceEager());
        $this->assertSame($forceEager, $operations['sylius_api_admin_product_get']->getForceEager());
    }

    #[Test]
    public function it_keeps_explicitly_defined_force_eager_on_sylius_operations(): void
    {
        $operations = $this->getOperations($this->createFactory(false));

        $this->assertTrue($operations['sylius_api_shop_product_get']->getForceEager());
    }

    #[Test]
    public function it_does_not_touch_operations_not_provided_by_sylius(): void
    {
        $operations = $this->getOperations($this->createFactory(false));

        $this->assertNull($operations['app_custom_get_collection']->getForceEager());
    }

    #[Test]
    public function it_does_not_touch_write_operations(): void
    {
        $operations = $this->getOperations($this->createFactory(false));

        $this->assertNull($operations['sylius_api_admin_product_put']->getForceEager());
    }

    #[Test]
    public function it_skips_resources_without_operations(): void
    {
        $factory = $this->createFactory(false, new ApiResource());

        $this->assertNull($factory->create(\stdClass::class)[0]->getOperations());
    }

    /** @return array<string, Operation> */
    private function getOperations(ForceEagerAwareResourceMetadataCollectionFactory $factory): array
    {
        return iterator_to_array($factory->create(\stdClass::class)[0]->getOperations());
    }

    private function createFactory(bool $forceEager, ?ApiResource $resource = null): ForceEagerAwareResourceMetadataCollectionFactory
    {
        $resource ??= (new ApiResource())->withOperations(new Operations([
            'sylius_api_admin_product_get_collection' => new GetCollection(name: 'sylius_api_admin_product_get_collection'),
            'sylius_api_admin_product_get' => new Get(name: 'sylius_api_admin_product_get'),
            'sylius_api_shop_product_get' => new Get(forceEager: true, name: 'sylius_api_shop_product_get'),
            'sylius_api_admin_product_put' => new Put(name: 'sylius_api_admin_product_put'),
            'app_custom_get_collection' => new GetCollection(name: 'app_custom_get_collection'),
        ]));

        $decorated = $this->createStub(ResourceMetadataCollectionFactoryInterface::class);
        $decorated->method('create')->willReturn(new ResourceMetadataCollection(\stdClass::class, [$resource]));

        return new ForceEagerAwareResourceMetadataCollectionFactory($decorated, $forceEager);
    }
}
