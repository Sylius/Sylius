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

namespace Tests\Sylius\Component\User\Canonicalizer;

use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Canonicalizer\Canonicalizer;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

final class CanonicalizerTest extends TestCase
{
    private Canonicalizer $canonicalizer;

    protected function setUp(): void
    {
        $this->canonicalizer = new Canonicalizer();
    }

    public function testShouldImplementCanonicalizerInterface(): void
    {
        $this->assertInstanceOf(CanonicalizerInterface::class, $this->canonicalizer);
    }

    public function testShouldConvertStringToLowerCase(): void
    {
        $expected = 'teststring';

        $result = $this->canonicalizer->canonicalize('tEsTsTrInG');

        $this->assertSame($expected, $result);
    }
}
