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
use Sylius\Bundle\CoreBundle\Validator\Constraints\CannotRevokeOwnAdministrationAccess;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CannotRevokeOwnAdministrationAccessValidator;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CannotRevokeOwnAdministrationAccessValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private MockObject&TokenStorageInterface $tokenStorage;

    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private CannotRevokeOwnAdministrationAccessValidator $validator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->validator = new CannotRevokeOwnAdministrationAccessValidator(
            $this->tokenStorage,
            $this->authorizationChecker,
        );
        $this->validator->initialize($this->context);
    }

    public function testItThrowsExceptionIfConstraintIsNotCannotRevokeOwnAdministrationAccess(): void
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

        $this->validator->validate(null, new CannotRevokeOwnAdministrationAccess());
    }

    public function testItThrowsExceptionIfValueIsNotAnAdminUser(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new \stdClass(), new CannotRevokeOwnAdministrationAccess());
    }

    public function testItDoesNothingIfAdminUserHasAdministrationAccess(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(true);

        $this->tokenStorage->expects($this->never())->method('getToken');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($adminUser, new CannotRevokeOwnAdministrationAccess());
    }

    public function testItDoesNothingIfThereIsNoLoggedInUser(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(false);

        $this->tokenStorage->method('getToken')->willReturn(null);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($adminUser, new CannotRevokeOwnAdministrationAccess());
    }

    public function testItDoesNothingIfLoggedInUserIsNotAnAdminUser(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(false);

        $this->tokenStorage->method('getToken')->willReturn($this->createTokenWithUser($this->createMock(UserInterface::class)));

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($adminUser, new CannotRevokeOwnAdministrationAccess());
    }

    public function testItDoesNothingIfAdminUserIsNotPersistedYet(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(false);
        $adminUser->method('getId')->willReturn(null);

        $loggedInAdminUser = $this->createMock(AdminUserInterface::class);
        $loggedInAdminUser->method('getId')->willReturn(1);

        $this->tokenStorage->method('getToken')->willReturn($this->createTokenWithUser($loggedInAdminUser));

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($adminUser, new CannotRevokeOwnAdministrationAccess());
    }

    public function testItDoesNothingIfLoggedInAdminUserHasNotBeenGrantedAdministrationAccess(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(false);
        $adminUser->method('getId')->willReturn(1);

        $loggedInAdminUser = $this->createMock(AdminUserInterface::class);
        $loggedInAdminUser->method('getId')->willReturn(1);

        $this->tokenStorage->method('getToken')->willReturn($this->createTokenWithUser($loggedInAdminUser));

        $this->authorizationChecker
            ->method('isGranted')
            ->with(AdminUserInterface::DEFAULT_ADMIN_ROLE)
            ->willReturn(false)
        ;

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($adminUser, new CannotRevokeOwnAdministrationAccess());
    }

    public function testItDoesNothingIfAdminUserIsNotTheLoggedInOne(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(false);
        $adminUser->method('getId')->willReturn(2);

        $loggedInAdminUser = $this->createMock(AdminUserInterface::class);
        $loggedInAdminUser->method('getId')->willReturn(1);

        $this->tokenStorage->method('getToken')->willReturn($this->createTokenWithUser($loggedInAdminUser));

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($adminUser, new CannotRevokeOwnAdministrationAccess());
    }

    public function testItAddsViolationIfLoggedInAdminUserRevokesTheirOwnAdministrationAccess(): void
    {
        $constraint = new CannotRevokeOwnAdministrationAccess();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('hasAdministrationAccess')->willReturn(false);
        $adminUser->method('getId')->willReturn(1);

        $loggedInAdminUser = $this->createMock(AdminUserInterface::class);
        $loggedInAdminUser->method('getId')->willReturn(1);

        $this->tokenStorage->method('getToken')->willReturn($this->createTokenWithUser($loggedInAdminUser));

        $this->authorizationChecker
            ->method('isGranted')
            ->with(AdminUserInterface::DEFAULT_ADMIN_ROLE)
            ->willReturn(true)
        ;

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

    private function createTokenWithUser(UserInterface $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
