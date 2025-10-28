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

namespace Tests\Sylius\Bundle\ApiBundle\SectionResolver;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiOrdersSubSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiUriBasedSectionResolver;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class ShopApiUriBasedSectionResolverTest extends TestCase
{
    private ShopApiUriBasedSectionResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ShopApiUriBasedSectionResolver('/api/v2/shop', 'orders');
    }

    public function testUriBasedSectionResolver(): void
    {
        self::assertInstanceOf(UriBasedSectionResolverInterface::class, $this->resolver);
    }

    public function testReturnsShopApiSectionIfPathStartsWithApiV2Shop(): void
    {
        $this->assertEquals(new ShopApiSection(), $this->resolver->getSection('/api/v2/shop/something'));

        $this->assertEquals(new ShopApiSection(), $this->resolver->getSection('/api/v2/shop'));
    }

    public function testReturnsShopApiOrdersSubsectionIfPathContainsOrders(): void
    {
        $this->assertEquals(new ShopApiOrdersSubSection(), $this->resolver->getSection('/api/v2/shop/orders'));
    }

    /**
     * @dataProvider nonMatchingPathsProvider
     */
    public function testThrowsAnExceptionIfPathDoesNotStartWithApiV2Shop(string $path): void
    {
        self::expectException(SectionCannotBeResolvedException::class);

        $this->resolver->getSection($path);
    }

    public static function nonMatchingPathsProvider(): array
    {
        return [
            ['/shop'],
            ['/admin'],
            ['/en_US/api'],
            ['/api/v1'],
            ['/api/v2'],
        ];
    }
}
