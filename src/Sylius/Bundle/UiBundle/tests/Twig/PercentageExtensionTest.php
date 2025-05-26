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

namespace Tests\Sylius\Bundle\UiBundle\Twig;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\Twig\PercentageExtension;
use Twig\Extension\ExtensionInterface;

final class PercentageExtensionTest extends TestCase
{
    private PercentageExtension $percentageExtension;

    protected function setUp(): void
    {
        $this->percentageExtension = new PercentageExtension();
    }

    public function testTwigExtension(): void
    {
        $this->assertInstanceOf(ExtensionInterface::class, $this->percentageExtension);
    }

    public function testReturnsFloatNumberAsPercentage(): void
    {
        $this->assertSame('11.2 %', $this->percentageExtension->getPercentage(0.112));
    }
}
