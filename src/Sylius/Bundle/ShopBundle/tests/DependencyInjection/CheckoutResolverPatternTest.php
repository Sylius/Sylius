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

namespace Tests\Sylius\Bundle\ShopBundle\DependencyInjection;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\PathRequestMatcher;

final class CheckoutResolverPatternTest extends TestCase
{
    private const CHECKOUT_PATTERN = '^/([^/]+/)?checkout/.+';

    #[Test]
    #[DataProvider('matchingPathsProvider')]
    public function it_matches_checkout_paths(string $path): void
    {
        $matcher = new PathRequestMatcher(self::CHECKOUT_PATTERN);
        $request = Request::create($path);

        $this->assertTrue($matcher->matches($request));
    }

    #[Test]
    #[DataProvider('nonMatchingPathsProvider')]
    public function it_does_not_match_non_checkout_paths(string $path): void
    {
        $matcher = new PathRequestMatcher(self::CHECKOUT_PATTERN);
        $request = Request::create($path);

        $this->assertFalse($matcher->matches($request));
    }

    public static function matchingPathsProvider(): iterable
    {
        yield 'default locale without prefix — address step' => ['/checkout/address'];
        yield 'default locale without prefix — shipping step' => ['/checkout/select-shipping'];
        yield 'default locale without prefix — payment step' => ['/checkout/select-payment'];
        yield 'default locale without prefix — complete step' => ['/checkout/complete'];
        yield 'non-default locale with prefix' => ['/nl/checkout/address'];
        yield 'locale with underscore' => ['/en_US/checkout/address'];
    }

    public static function nonMatchingPathsProvider(): iterable
    {
        yield 'cart summary' => ['/cart'];
        yield 'homepage' => ['/'];
        yield 'product page' => ['/nl/products/t-shirt'];
        yield 'admin path' => ['/admin/orders'];
        yield 'checkout without trailing segment' => ['/checkout'];
        yield 'checkout without trailing slash and segment' => ['/nl/checkout'];
    }
}
