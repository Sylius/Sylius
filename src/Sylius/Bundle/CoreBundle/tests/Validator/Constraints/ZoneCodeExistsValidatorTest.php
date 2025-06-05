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
use Sylius\Bundle\CoreBundle\Validator\Constraints\ZoneCodeExists;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ZoneCodeExistsValidator;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ZoneCodeExistsValidatorTest extends TestCase
{
    private MockObject&RepositoryInterface $zoneRepository;

    private ExecutionContextInterface&MockObject $executionContext;

    private ZoneCodeExistsValidator $validator;

    protected function setUp(): void
    {
        $this->zoneRepository = $this->createMock(RepositoryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ZoneCodeExistsValidator($this->zoneRepository);
        $this->validator->initialize($this->executionContext);
    }

    public function testThrowsExceptionIfConstraintIsNotInstanceOfZoneCodeExists(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('zone_code', $this->createMock(Constraint::class));
    }

    public function testDoesNothingIfValueIsEmpty(): void
    {
        $this->executionContext->expects($this->never())->method('buildViolation');
        $this->zoneRepository->expects($this->never())->method('findOneBy');

        $this->validator->validate('', new ZoneCodeExists());
    }

    public function testDoesNothingIfZoneWithCodeExists(): void
    {
        $zone = $this->createMock(ZoneInterface::class);

        $this->zoneRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'zone_code'])
            ->willReturn($zone)
        ;

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('zone_code', new ZoneCodeExists());
    }

    public function testAddsViolationIfZoneWithCodeDoesNotExist(): void
    {
        $constraint = new ZoneCodeExists();
        $constraint->message = 'sylius.zone.code.not_exist';

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ code }}', 'zone_code')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with('sylius.zone.code.not_exist')
            ->willReturn($violationBuilder)
        ;

        $this->zoneRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'zone_code'])
            ->willReturn(null)
        ;

        $this->validator->validate('zone_code', $constraint);
    }
}
