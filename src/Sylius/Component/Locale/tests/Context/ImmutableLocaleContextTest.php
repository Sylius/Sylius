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

namespace Tests\Sylius\Component\Locale\Context;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\ImmutableLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ImmutableLocaleContextTest extends TestCase
{
    private ImmutableLocaleContext $immutableLocaleContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->immutableLocaleContext = new ImmutableLocaleContext('pl_PL');
    }

    public function testALocaleContext(): void
    {
        self::assertInstanceOf(LocaleContextInterface::class, $this->immutableLocaleContext);
    }

    public function testGetsALocaleCode(): void
    {
        self::assertSame('pl_PL', $this->immutableLocaleContext->getLocaleCode());
    }
}
