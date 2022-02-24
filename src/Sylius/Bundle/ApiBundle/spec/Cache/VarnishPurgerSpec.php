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

namespace spec\Sylius\Bundle\ApiBundle\Cache;

use ApiPlatform\Core\HttpCache\PurgerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

final class VarnishPurgerSpec extends ObjectBehavior
{
    function let(PurgerInterface $basePurger, UrlMatcherInterface $urlMatcher): void
    {
        $this->beConstructedWith($basePurger, $urlMatcher);
    }

    function it_implements_purger_interface(): void
    {
        $this->shouldImplement(PurgerInterface::class);
    }

    function it_does_nothing_if_processing_iris_are_not_from_admin_section(
        PurgerInterface $basePurger,
        UrlMatcherInterface $urlMatcher
    ): void {
        $urlMatcher->match(Argument::any())->shouldNotBeCalled();

        $basePurger->purge(['/shop/product-variants/MUG', '/shop/product-variants/T-SHIRT'])->shouldBeCalled();

        $this->purge(['/shop/product-variants/MUG', '/shop/product-variants/T-SHIRT']);
    }

    function it_does_nothing_if_processing_admin_iris_does_not_manifest_in_shop(
        PurgerInterface $basePurger,
        UrlMatcherInterface $urlMatcher
    ): void {
        $urlMatcher->match('/shop/administrators')->willThrow(\Exception::class);

        $basePurger->purge(['/admin/administrators', '/shop/product-variants/T-SHIRT']);

        $this->purge(['/admin/administrators', '/shop/product-variants/T-SHIRT']);
    }

    function it_adds_shop_iris_to_purge_if_they_are_changed_in_admin(
        PurgerInterface $basePurger,
        UrlMatcherInterface $urlMatcher
    ): void {
        $urlMatcher->match('/shop/product-variants/MUG')->shouldBeCalled();
        $urlMatcher->match('/shop/product-variants/T-SHIRT')->shouldBeCalled();

        $basePurger
            ->purge(['/shop/product-variants/MUG', '/admin/product-variants/MUG', '/shop/product-variants/T-SHIRT', '/admin/product-variants/T-SHIRT'])
            ->shouldBeCalled()
        ;

        $this->purge(['/admin/product-variants/MUG', '/admin/product-variants/T-SHIRT']);
    }
}
