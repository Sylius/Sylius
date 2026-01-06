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

namespace Tests\Sylius\Bundle\ProductBundle\Validator\Constraint;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Validator\Constraint\UniqueSimpleProductCode;
use Symfony\Component\Validator\Constraint;

final class UniqueSimpleProductCodeTest extends TestCase
{
    private UniqueSimpleProductCode $uniqueSimpleProductCode;

    protected function setUp(): void
    {
        $this->uniqueSimpleProductCode = new UniqueSimpleProductCode();
    }

    public function testClassConstraint(): void
    {
        $this->assertSame(Constraint::CLASS_CONSTRAINT, $this->uniqueSimpleProductCode->getTargets());
    }
}
