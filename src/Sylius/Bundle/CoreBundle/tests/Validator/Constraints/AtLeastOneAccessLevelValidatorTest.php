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
use Sylius\Bundle\CoreBundle\Validator\Constraints\AtLeastOneAccessLevel;
use Sylius\Bundle\CoreBundle\Validator\Constraints\AtLeastOneAccessLevelValidator;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class AtLeastOneAccessLevelValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private AtLeastOneAccessLevelValidator $validator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new AtLeastOneAccessLevelValidator();
        $this->validator->initialize($this->context);
    }

    public function testItThrowsExceptionIfConstraintIsNotAtLeastOneAccessLevel(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(
            $this->createMock(AdminUserInterface::class),
            $this->createMock(Constraint::class),
        );
    }

    public function testItDoesNothingIfValueIsNull(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(null, new AtLeastOneAccessLevel());
    }

    public function testItThrowsExceptionIfValueIsNotAnAdminUser(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new \stdClass(), new AtLeastOneAccessLevel());
    }

    public function testItDoesNothingIfAdminUserHasAdministrationAccess(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(true);
        $adminUser->method('hasApiAccess')->willReturn(false);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($adminUser, new AtLeastOneAccessLevel());
    }

    public function testItDoesNothingIfAdminUserHasApiAccess(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(false);
        $adminUser->method('hasApiAccess')->willReturn(true);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($adminUser, new AtLeastOneAccessLevel());
    }

    public function testItAddsViolationIfAdminUserHasNoAccessLevel(): void
    {
        $constraint = new AtLeastOneAccessLevel();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(false);
        $adminUser->method('hasApiAccess')->willReturn(false);

        $violationBuilder
            ->expects($this->once())
            ->method('atPath')
            ->with('administrationAccess')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate($adminUser, $constraint);
    }
}
