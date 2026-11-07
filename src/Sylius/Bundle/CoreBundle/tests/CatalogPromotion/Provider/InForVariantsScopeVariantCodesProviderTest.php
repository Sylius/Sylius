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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\InForVariantsScopeVariantCodesProvider;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesInScopeProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;

#[CoversClass(InForVariantsScopeVariantCodesProvider::class)]
final class InForVariantsScopeVariantCodesProviderTest extends TestCase
{
    private InForVariantsScopeVariantCodesProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new InForVariantsScopeVariantCodesProvider();
    }

    public function testImplementsVariantCodesInScopeProviderInterface(): void
    {
        $this->assertInstanceOf(VariantCodesInScopeProviderInterface::class, $this->provider);
    }

    public function testReturnsVariantCodesFromScopeConfiguration(): void
    {
        $scope = $this->createStub(CatalogPromotionScopeInterface::class);
        $scope->method('getConfiguration')->willReturn(['variants' => ['FIRST_VARIANT', 'SECOND_VARIANT']]);

        $this->assertSame(['FIRST_VARIANT', 'SECOND_VARIANT'], $this->provider->provide($scope));
    }

    public function testReturnsAListEvenIfConfigurationKeysAreNotSequential(): void
    {
        $scope = $this->createStub(CatalogPromotionScopeInterface::class);
        $scope->method('getConfiguration')->willReturn(['variants' => [3 => 'FIRST_VARIANT', 7 => 'SECOND_VARIANT']]);

        $this->assertSame(['FIRST_VARIANT', 'SECOND_VARIANT'], $this->provider->provide($scope));
    }

    public function testReturnsNoVariantCodesIfNoVariantIsConfigured(): void
    {
        $scope = $this->createStub(CatalogPromotionScopeInterface::class);
        $scope->method('getConfiguration')->willReturn(['variants' => []]);

        $this->assertSame([], $this->provider->provide($scope));
    }

    public function testThrowsExceptionIfScopeDoesNotContainVariantsConfiguration(): void
    {
        $scope = $this->createStub(CatalogPromotionScopeInterface::class);
        $scope->method('getConfiguration')->willReturn(['FOO' => ['BOO']]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This rule should have configured variants');

        $this->provider->provide($scope);
    }
}
