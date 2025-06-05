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

namespace Tests\Sylius\Bundle\ShopBundle\SectionResolver;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopCustomerAccountSubSection;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopUriBasedSectionResolver;

final class ShopUriBasedSectionResolverTest extends TestCase
{
    private ShopUriBasedSectionResolver $shopUriBasedSectionResolver;

    protected function setUp(): void
    {
        $this->shopUriBasedSectionResolver = new ShopUriBasedSectionResolver();
    }

    public function testItUriBasedSectionResolver(): void
    {
        $this->assertInstanceOf(UriBasedSectionResolverInterface::class, $this->shopUriBasedSectionResolver);
    }

    public function testReturnsShopByDefault(): void
    {
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/api/something'));
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/api'));
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/ap'));
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/shop'));
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/admin/asd'));
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/en_US/api'));
    }

    public function testUsesAccountPrefixForCustomerAccountSubsectionByDefault(): void
    {
        $this->assertEquals(new ShopCustomerAccountSubSection(), $this->shopUriBasedSectionResolver->getSection('/account'));
        $this->assertEquals(new ShopCustomerAccountSubSection(), $this->shopUriBasedSectionResolver->getSection('/api/account'));
        $this->assertEquals(new ShopCustomerAccountSubSection(), $this->shopUriBasedSectionResolver->getSection('/en_US/account'));
        $this->assertEquals(new ShopCustomerAccountSubSection(), $this->shopUriBasedSectionResolver->getSection('/account/random'));
    }

    public function testMayHaveAccountPrefixCustomized(): void
    {
        $this->shopUriBasedSectionResolver = new ShopUriBasedSectionResolver('konto');

        $this->assertEquals(new ShopCustomerAccountSubSection(), $this->shopUriBasedSectionResolver->getSection('/konto'));
        $this->assertEquals(new ShopCustomerAccountSubSection(), $this->shopUriBasedSectionResolver->getSection('/api/konto'));
        $this->assertEquals(new ShopCustomerAccountSubSection(), $this->shopUriBasedSectionResolver->getSection('/en_US/konto'));
        $this->assertEquals(new ShopCustomerAccountSubSection(), $this->shopUriBasedSectionResolver->getSection('/konto/random'));

        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/account'));
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/api/account'));
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/en_US/account'));
        $this->assertEquals(new ShopSection(), $this->shopUriBasedSectionResolver->getSection('/account/random'));
    }
}
