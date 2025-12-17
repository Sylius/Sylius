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

namespace Tests\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraint;

final class ProvinceAddressConstraintTest extends TestCase
{
    private ProvinceAddressConstraint $provinceAddressConstraint;

    protected function setUp(): void
    {
        $this->provinceAddressConstraint = new ProvinceAddressConstraint();
    }

    public function testHasTargets(): void
    {
        $this->assertSame('class', $this->provinceAddressConstraint->getTargets());
    }

    public function testValidatedBy(): void
    {
        $this->assertSame('sylius_province_address_validator', $this->provinceAddressConstraint->validatedBy());
    }
}
