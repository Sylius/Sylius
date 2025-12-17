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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Generator;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Telemetry\Generator\InstallationIdGenerator;
use Symfony\Component\HttpFoundation\Request;

final class InstallationIdGeneratorTest extends TestCase
{
    public function test_it_generates_uuid_v5_like_string(): void
    {
        $request = Request::create('https://example.com');
        $generator = new InstallationIdGenerator('salt-value');

        self::assertRegExp(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $generator->generate($request),
        );
    }

    public function test_it_generates_deterministic_identifier(): void
    {
        $request = Request::create('https://example.com');
        $generator = new InstallationIdGenerator('salt-value');

        self::assertSame($generator->generate($request), $generator->generate($request));
    }

    public function test_it_returns_empty_string_when_salt_is_empty(): void
    {
        $request = Request::create('https://example.com');
        $generator = new InstallationIdGenerator('   ');

        self::assertSame('', $generator->generate($request));
    }

    public function test_different_hostnames_produce_different_ids(): void
    {
        $generator = new InstallationIdGenerator('salt-value');

        $request1 = Request::create('https://shop1.example.com');
        $request2 = Request::create('https://shop2.example.com');

        self::assertNotSame($generator->generate($request1), $generator->generate($request2));
    }

    public function test_same_hostname_produces_same_id(): void
    {
        $generator = new InstallationIdGenerator('salt-value');

        $request1 = Request::create('https://example.com/admin');
        $request2 = Request::create('https://example.com/shop');

        self::assertSame($generator->generate($request1), $generator->generate($request2));
    }
}
