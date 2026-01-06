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

namespace Tests\Sylius\Bundle\MoneyBundle\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\MoneyBundle\Form\DataTransformer\SyliusMoneyTransformer;

final class SyliusMoneyTransformerTest extends TestCase
{
    public function testReturnsNullIfEmptyStringGiven(): void
    {
        $syliusMoneyTransformer = new SyliusMoneyTransformer(null, null, null, 100);

        self::assertNull($syliusMoneyTransformer->reverseTransform(''));
    }

    public function testConvertsStringToAnInteger(): void
    {
        $syliusMoneyTransformer = new SyliusMoneyTransformer(null, null, null, 100);

        self::assertSame(410, $syliusMoneyTransformer->reverseTransform('4.10'));
    }
}
