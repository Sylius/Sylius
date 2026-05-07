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

namespace Tests\Sylius\Bundle\ShippingBundle\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\ShippingBundle\Validator\Constraint\ValidDeliveryTimeRange;
use Sylius\Bundle\ShippingBundle\Validator\ValidDeliveryTimeRangeValidator;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class ValidDeliveryTimeRangeValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): \Symfony\Component\Validator\ConstraintValidatorInterface
    {
        return new ValidDeliveryTimeRangeValidator();
    }

    public function testThrowsWhenConstraintIsInvalid(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);
        /** @var ShippingMethodInterface&MockObject $method */
        $method = $this->createMock(ShippingMethodInterface::class);

        $this->validator->validate($method, $constraint);
    }

    public function testThrowsWhenValueIsNotShippingMethod(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new \stdClass(), new ValidDeliveryTimeRange());
    }

    public function testDoesNotAddViolationWhenBothValuesAreNull(): void
    {
        /** @var ShippingMethodInterface&MockObject $method */
        $method = $this->createMock(ShippingMethodInterface::class);
        $method->method('getMinDeliveryTimeDays')->willReturn(null);
        $method->method('getMaxDeliveryTimeDays')->willReturn(null);

        $this->validator->validate($method, new ValidDeliveryTimeRange());
        $this->assertNoViolation();
    }

    public function testDoesNotAddViolationWhenMaxIsGreaterOrEqualMin(): void
    {
        /** @var ShippingMethodInterface&MockObject $method */
        $method = $this->createMock(ShippingMethodInterface::class);
        $method->method('getMinDeliveryTimeDays')->willReturn(3);
        $method->method('getMaxDeliveryTimeDays')->willReturn(3);

        $this->validator->validate($method, new ValidDeliveryTimeRange());
        $this->assertNoViolation();
    }

    public function testAddsViolationWhenMaxIsLowerThanMin(): void
    {
        /** @var ShippingMethodInterface&MockObject $method */
        $method = $this->createMock(ShippingMethodInterface::class);
        $method->method('getMinDeliveryTimeDays')->willReturn(5);
        $method->method('getMaxDeliveryTimeDays')->willReturn(3);

        $this->validator->validate($method, new ValidDeliveryTimeRange());

        $this
            ->buildViolation((new ValidDeliveryTimeRange())->message)
            ->atPath('property.path.maxDeliveryTimeDays')
            ->assertRaised()
        ;
    }
}
