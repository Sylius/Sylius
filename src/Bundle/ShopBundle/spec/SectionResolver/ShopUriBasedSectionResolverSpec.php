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

namespace spec\Sylius\Bundle\ShopBundle\SectionResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopCustomerAccountSubSection;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;

final class ShopUriBasedSectionResolverSpec extends ObjectBehavior
{
    function it_it_uri_based_section_resolver(): void
    {
        $this->shouldImplement(UriBasedSectionResolverInterface::class);
    }

    function it_returns_shop_by_default(): void
    {
        $this->getSection('/api/something')->shouldBeLike(new ShopSection());
        $this->getSection('/api')->shouldBeLike(new ShopSection());
        $this->getSection('/ap')->shouldBeLike(new ShopSection());
        $this->getSection('/shop')->shouldBeLike(new ShopSection());
        $this->getSection('/admin/asd')->shouldBeLike(new ShopSection());
        $this->getSection('/en_US/api')->shouldBeLike(new ShopSection());
    }

    function it_uses_account_prefix_for_customer_account_subsection_by_default(): void
    {
        $this->getSection('/account')->shouldBeLike(new ShopCustomerAccountSubSection());
        $this->getSection('/api/account')->shouldBeLike(new ShopCustomerAccountSubSection());
        $this->getSection('/en_US/account')->shouldBeLike(new ShopCustomerAccountSubSection());
        $this->getSection('/account/random')->shouldBeLike(new ShopCustomerAccountSubSection());
    }

    function it_may_have_account_prefix_customized(): void
    {
        $this->beConstructedWith('konto');

        $this->getSection('/konto')->shouldBeLike(new ShopCustomerAccountSubSection());
        $this->getSection('/konto')->shouldBeLike(new ShopCustomerAccountSubSection());
        $this->getSection('/api/konto')->shouldBeLike(new ShopCustomerAccountSubSection());
        $this->getSection('/en_US/konto')->shouldBeLike(new ShopCustomerAccountSubSection());
        $this->getSection('/konto/random')->shouldBeLike(new ShopCustomerAccountSubSection());

        $this->getSection('/account')->shouldBeLike(new ShopSection());
        $this->getSection('/account')->shouldBeLike(new ShopSection());
        $this->getSection('/api/account')->shouldBeLike(new ShopSection());
        $this->getSection('/en_US/account')->shouldBeLike(new ShopSection());
        $this->getSection('/account/random')->shouldBeLike(new ShopSection());
    }
}
