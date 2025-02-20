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

namespace Sylius\Bundle\AdminBundle\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminFirewallTest extends WebTestCase
{
    private string $adminRegexp;

    public function setUp(): void
    {
        self::bootKernel();

        $this->adminRegexp = static::getContainer()->getParameter('sylius.security.admin_regex');
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
        $this->assertSame($expected, (bool) preg_match('#'.$this->adminRegexp.'#', $url));
    }

    /**
     * @return iterable<array{string, bool}>
     */
    public static function getUrls(): iterable
    {
        yield 'Admin url without ending slash' => ['/admin', true];
        yield 'Admin url with ending slash' => ['/admin/', true];
        yield 'Admin url with suffix' => ['/admin/foo', true];

        yield 'Admin url without starting slash' => ['admin', false];
        yield 'Random url starting with admin' => ['/adminfoo', false];
    }
}
