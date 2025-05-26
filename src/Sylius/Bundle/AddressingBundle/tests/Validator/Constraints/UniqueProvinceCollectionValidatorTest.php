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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\UniqueProvinceCollection;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\UniqueProvinceCollectionValidator;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UniqueProvinceCollectionValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private UniqueProvinceCollectionValidator $uniqueProvinceCollectionValidator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->uniqueProvinceCollectionValidator = new UniqueProvinceCollectionValidator();
        $this->uniqueProvinceCollectionValidator->initialize($this->executionContext);
    }

    public function testAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidatorInterface::class, $this->uniqueProvinceCollectionValidator);
    }

    public function testThrowsExceptionWhenValueIsNotACollection(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->uniqueProvinceCollectionValidator->validate(new \stdClass(), new UniqueProvinceCollection());
    }

    public function testThrowsExceptionWhenCollectionContainsSomethingOtherThanProvinces(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->uniqueProvinceCollectionValidator->validate(new ArrayCollection([new \stdClass()]), new UniqueProvinceCollection());
    }

    public function testThrowsExceptionWhenConstraintIsNotAUniqueProvinceCollectionCodes(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        $this->uniqueProvinceCollectionValidator->validate(new ArrayCollection(), $constraint);
    }

    public function testDoesNothingWhenCollectionIsEmpty(): void
    {
        /** @var ExecutionContextInterface&MockObject $context */
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('addViolation');

        $this->uniqueProvinceCollectionValidator->validate(new ArrayCollection(), new UniqueProvinceCollection());
    }

    public function testDoesNothingWhenAllProvincesHaveUniqueCodes(): void
    {
        /** @var ProvinceInterface&MockObject $firstProvince */
        $firstProvince = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $secondProvince */
        $secondProvince = $this->createMock(ProvinceInterface::class);

        $firstProvince->expects($this->atLeastOnce())->method('getCode')->willReturn('first');
        $firstProvince->expects($this->once())->method('getName')->willReturn('first');
        $secondProvince->expects($this->atLeastOnce())->method('getCode')->willReturn('second');
        $secondProvince->expects($this->once())->method('getName')->willReturn('second');
        $this->executionContext->expects($this->never())->method('addViolation');

        $this->uniqueProvinceCollectionValidator->validate(
            new ArrayCollection([$firstProvince, $secondProvince]),
            new UniqueProvinceCollection(),
        );
    }

    public function testChecksUniquenessWithIncompleteCodes(): void
    {
        /** @var ProvinceInterface&MockObject $province */
        $province = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $sameProvinceWithCode */
        $sameProvinceWithCode = $this->createMock(ProvinceInterface::class);
        $constraint = new UniqueProvinceCollection();

        $province->expects($this->atLeastOnce())->method('getCode')->willReturn(null);
        $province->expects($this->atLeastOnce())->method('getName')->willReturn('name');
        $sameProvinceWithCode->expects($this->atLeastOnce())->method('getCode')->willReturn('code');
        $sameProvinceWithCode->expects($this->once())->method('getName')->willReturn('name');
        $this->executionContext->expects($this->once())->method('addViolation')->with($constraint->message);

        $this->uniqueProvinceCollectionValidator->validate(
            new ArrayCollection([
                $province,
                $sameProvinceWithCode,
            ]),
            $constraint,
        );
    }

    public function testChecksUniquenessWithIncompleteNames(): void
    {
        /** @var ProvinceInterface&MockObject $province */
        $province = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $sameProvinceWithName */
        $sameProvinceWithName = $this->createMock(ProvinceInterface::class);
        $constraint = new UniqueProvinceCollection();

        $province->expects($this->atLeastOnce())->method('getCode')->willReturn('code');
        $province->expects($this->once())->method('getName')->willReturn(null);
        $sameProvinceWithName->expects($this->atLeastOnce())->method('getCode')->willReturn('code');
        $sameProvinceWithName->expects($this->once())->method('getName')->willReturn('name');
        $this->executionContext->expects($this->once())->method('addViolation')->with($constraint->message);

        $this->uniqueProvinceCollectionValidator->validate(
            new ArrayCollection([
                $province,
                $sameProvinceWithName,
            ]),
            $constraint,
        );
    }

    public function testAddsViolationWhenCodesAreDuplicated(): void
    {
        /** @var ProvinceInterface&MockObject $firstProvince */
        $firstProvince = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $secondProvince */
        $secondProvince = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $thirdProvince */
        $thirdProvince = $this->createMock(ProvinceInterface::class);
        $constraint = new UniqueProvinceCollection();

        $firstProvince->expects($this->atLeastOnce())->method('getCode')->willReturn('same');
        $firstProvince->expects($this->once())->method('getName')->willReturn('first');
        $secondProvince->expects($this->atLeastOnce())->method('getCode')->willReturn('same');
        $secondProvince->expects($this->once())->method('getName')->willReturn('second');
        $thirdProvince->expects($this->atLeastOnce())->method('getCode')->willReturn('different');
        $thirdProvince->expects($this->never())->method('getName')->willReturn('third');
        $this->executionContext->expects($this->once())->method('addViolation')->with($constraint->message);

        $this->uniqueProvinceCollectionValidator->validate(
            new ArrayCollection([
                $firstProvince,
                $secondProvince,
                $thirdProvince,
            ]),
            $constraint,
        );
    }

    public function testAddsViolationWhenNamesAreDuplicated(): void
    {
        /** @var ProvinceInterface&MockObject $firstProvince */
        $firstProvince = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $secondProvince */
        $secondProvince = $this->createMock(ProvinceInterface::class);
        /** @var ProvinceInterface&MockObject $thirdProvince */
        $thirdProvince = $this->createMock(ProvinceInterface::class);
        $constraint = new UniqueProvinceCollection();

        $firstProvince->expects($this->atLeastOnce())->method('getCode')->willReturn('first');
        $firstProvince->expects($this->once())->method('getName')->willReturn('first');
        $secondProvince->expects($this->atLeastOnce())->method('getCode')->willReturn('second');
        $secondProvince->expects($this->once())->method('getName')->willReturn('same');
        $thirdProvince->expects($this->atLeastOnce())->method('getCode')->willReturn('third');
        $thirdProvince->expects($this->once())->method('getName')->willReturn('same');
        $this->executionContext->expects($this->once())->method('addViolation')->with($constraint->message);

        $this->uniqueProvinceCollectionValidator->validate(
            new ArrayCollection([
                $firstProvince,
                $secondProvince,
                $thirdProvince,
            ]),
            $constraint,
        );
    }
}
