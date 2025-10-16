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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProvinceCodeExists;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProvinceCodeExistsValidator;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProvinceCodeExistsValidatorTest extends TestCase
{
    private MockObject&RepositoryInterface $provinceRepository;

    private ExecutionContextInterface&MockObject $context;

    private ProvinceCodeExistsValidator $validator;

    protected function setUp(): void
    {
        $this->provinceRepository = $this->createMock(RepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new ProvinceCodeExistsValidator($this->provinceRepository);
        $this->validator->initialize($this->context);
    }

    public function testItThrowsExceptionIfConstraintIsNotProvinceCodeExists(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('province_code', $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfValueIsEmpty(): void
    {
        $this->provinceRepository->expects($this->never())->method('findOneBy');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('', new ProvinceCodeExists());
    }

    public function testItDoesNothingIfProvinceWithGivenCodeExists(): void
    {
        $province = $this->createMock(ProvinceInterface::class);

        $this->provinceRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'province_code'])
            ->willReturn($province)
        ;

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('province_code', new ProvinceCodeExists());
    }

    public function testItAddsViolationIfProvinceWithGivenCodeDoesNotExist(): void
    {
        $constraint = new ProvinceCodeExists();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->provinceRepository
            ->method('findOneBy')
            ->with(['code' => 'province_code'])
            ->willReturn(null)
        ;

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ code }}', 'province_code')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('province_code', $constraint);
    }
}
