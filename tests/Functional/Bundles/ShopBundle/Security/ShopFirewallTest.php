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

namespace Sylius\Tests\Functional\Bundles\ShopBundle\Security;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ShopFirewallTest extends KernelTestCase
{
    private string $shopRegexp;

    public function setUp(): void
    {
        self::bootKernel();

        $this->shopRegexp = static::getContainer()->getParameter('sylius.security.shop_regex');
    }

    /**
     * @dataProvider getUrls
     */
    public function test_regexp(string $url, bool $expected): void
    {
        /**
         * Symfony is using # char as delimiter
         * @see \Symfony\Bundle\SecurityBundle\DependencyInjection\MainConfiguration::addFirewallsSection()
         */
        $this->assertSame($expected, (bool) preg_match('#'.$this->shopRegexp.'#', $url));
    }

    /**
     * @return iterable<array{string, bool}>
     */
    public static function getUrls(): iterable
    {
        yield 'Homepage with slash' => ['/', true];
        yield 'Homepage without slash' => ['', true];
        yield 'Random foo' => ['/foo', true];
        yield 'Random foo without starting slash' => ['foo', true];
        yield 'Random url starting with admin' => ['/adminfoo', true];
        yield 'Random url starting with admin and suffix' => ['/adminfoo/bar', true];
        yield 'Random url starting with api' => ['/apifoo', true];
        yield 'Random url starting with api and suffix' => ['/apifoo/bar', true];
        yield 'Media folder without ending slash' =>  ['/media', true]; // Maybe this one should be false
        yield 'Random url starting with media' => ['/mediafoo', true];
        yield 'Random url starting with media and suffix' => ['/mediafoo/bar', true];

        yield 'Admin url without ending slash' => ['/admin', false];
        yield 'API url and suffix' => ['/api/foo', false];
        yield 'API url without ending slash' => ['/api', false];
        yield 'Media url and suffix' => ['/media/foo', false];
    }
}
