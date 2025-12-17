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

namespace Tests\Sylius\Bundle\CoreBundle\Telemetry\Generator;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Telemetry\Generator\InstallationIdGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class InstallationIdGeneratorTest extends TestCase
{
    public function test_it_generates_uuid_v5_like_string(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://example.com'));

        $generator = new InstallationIdGenerator('salt-value', $requestStack);

        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $generator->generate(),
        );
    }

    public function test_it_generates_deterministic_identifier(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://example.com'));

        $generator = new InstallationIdGenerator('salt-value', $requestStack);

        self::assertSame($generator->generate(), $generator->generate());
    }

    public function test_it_returns_empty_string_when_salt_is_empty(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://example.com'));

        $generator = new InstallationIdGenerator('   ', $requestStack);

        self::assertSame('', $generator->generate());
    }

    public function test_it_returns_empty_string_when_hostname_is_empty(): void
    {
        $requestStack = new RequestStack();

        $generator = new InstallationIdGenerator('salt-value', $requestStack);

        $result = $generator->generate();

        self::assertIsString($result);
    }
}
