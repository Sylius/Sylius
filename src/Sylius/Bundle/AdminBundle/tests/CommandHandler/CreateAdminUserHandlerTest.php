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

namespace Tests\Sylius\Bundle\AdminBundle\CommandHandler;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Command\CreateAdminUser;
use Sylius\Bundle\AdminBundle\CommandHandler\CreateAdminUserHandler;
use Sylius\Bundle\AdminBundle\Exception\CreateAdminUserFailedException;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AllowMockObjectsWithoutExpectations]
final class CreateAdminUserHandlerTest extends TestCase
{
    private const NON_CANONICALIZED_EMAIL = 'SYLius@exaMPLE.com';

    private const EMAIL = 'sylius@example.com';

    private const USERNAME = 'username';

    private const PASSWORD = 'password';

    private const FIRST_NAME = 'First name';

    private const LAST_NAME = 'Last name';

    private const LOCALE_CODE = 'en_US';

    private const ENABLED = true;

    private const GROUPS = ['sylius', 'sylius_user_create'];

    private MockObject $adminUserFactory;

    private MockObject $adminUser;

    private MockObject $canonicalizer;

    private MockObject $validator;

    private MockObject $adminUserRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUserFactory = $this->createMock(FactoryInterface::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);
        $this->canonicalizer = $this->createMock(CanonicalizerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->adminUserRepository = $this->createMock(UserRepositoryInterface::class);
    }

    #[Test]
    public function it_creates_an_admin_user_if_there_is_no_violations(): void
    {
        $constraintViolationList = new ConstraintViolationList([]);

        $this->arrangePartially($constraintViolationList);

        $this->adminUserRepository->expects($this->once())->method('add');

        $createAdminUserHandler = $this->createAdminUserHandler();

        $createAdminUserHandler($this->createAdminUserMessage());
    }

    /**
     * @param array<array-key, string> $expectedAddedRoles
     * @param array<array-key, string> $expectedRemovedRoles
     */
    #[Test]
    #[DataProvider('accessLevelsProvider')]
    public function it_assigns_roles_according_to_the_given_access_levels(
        bool $administrationAccess,
        bool $apiAccess,
        array $expectedAddedRoles,
        array $expectedRemovedRoles,
    ): void {
        $constraintViolationList = new ConstraintViolationList([]);

        $this->arrangePartially($constraintViolationList);

        $addedRoles = [];
        $removedRoles = [];

        $this->adminUser
            ->method('addRole')
            ->willReturnCallback(function (string $role) use (&$addedRoles): void {
                $addedRoles[] = $role;
            })
        ;
        $this->adminUser
            ->method('removeRole')
            ->willReturnCallback(function (string $role) use (&$removedRoles): void {
                $removedRoles[] = $role;
            })
        ;

        $this->adminUserRepository->expects($this->once())->method('add');

        $createAdminUserHandler = $this->createAdminUserHandler();

        $createAdminUserHandler($this->createAdminUserMessage($administrationAccess, $apiAccess));

        self::assertSame($expectedAddedRoles, $addedRoles);
        self::assertSame($expectedRemovedRoles, $removedRoles);
    }

    /** @return iterable<string, array{bool, bool, array<array-key, string>, array<array-key, string>}> */
    public static function accessLevelsProvider(): iterable
    {
        yield 'both access levels' => [
            true,
            true,
            [AdminUserInterface::DEFAULT_ADMIN_ROLE, AdminUserInterface::API_ACCESS_ROLE],
            [],
        ];

        yield 'administration access only' => [
            true,
            false,
            [AdminUserInterface::DEFAULT_ADMIN_ROLE],
            [AdminUserInterface::API_ACCESS_ROLE],
        ];

        yield 'api access only' => [
            false,
            true,
            [AdminUserInterface::API_ACCESS_ROLE],
            [AdminUserInterface::DEFAULT_ADMIN_ROLE],
        ];

        yield 'no access levels' => [
            false,
            false,
            [],
            [AdminUserInterface::DEFAULT_ADMIN_ROLE, AdminUserInterface::API_ACCESS_ROLE],
        ];
    }

    #[Test]
    public function it_throws_an_exception_if_violates_any_constraints(): void
    {
        $firstConstraintViolation = new ConstraintViolation('first_violation_error', '', [], '', '', '');
        $secondConstraintViolation = new ConstraintViolation('second_violation_error', '', [], '', '', '');

        $constraintViolationList = new ConstraintViolationList([$firstConstraintViolation, $secondConstraintViolation]);

        $this->arrangePartially($constraintViolationList);

        $this->adminUserRepository->expects($this->never())->method('add');

        $createAdminUserHandler = $this->createAdminUserHandler();

        self::expectException(CreateAdminUserFailedException::class);
        self::expectExceptionMessage('first_violation_error' . \PHP_EOL . 'second_violation_error');

        $createAdminUserHandler($this->createAdminUserMessage());
    }

    private function arrangePartially(ConstraintViolationList $validationErrorsList): void
    {
        $this->adminUserFactory->expects($this->once())->method('createNew')->willReturn($this->adminUser);

        $this->canonicalizer
            ->expects($this->once())
            ->method('canonicalize')
            ->with(self::NON_CANONICALIZED_EMAIL)
            ->willReturn(self::EMAIL)
        ;

        $this->adminUser->expects($this->once())->method('setEmail')->with(self::EMAIL);
        $this->adminUser->expects($this->once())->method('setUsername')->with(self::USERNAME);
        $this->adminUser->expects($this->once())->method('setPlainPassword')->with(self::PASSWORD);
        $this->adminUser->expects($this->once())->method('setFirstName')->with(self::FIRST_NAME);
        $this->adminUser->expects($this->once())->method('setLastName')->with(self::LAST_NAME);
        $this->adminUser->expects($this->once())->method('setLocaleCode')->with(self::LOCALE_CODE);
        $this->adminUser->expects($this->once())->method('setEnabled')->with(self::ENABLED);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($this->adminUser, null, self::GROUPS)
            ->willReturn($validationErrorsList)
        ;
    }

    private function createAdminUserHandler(): CreateAdminUserHandler
    {
        return new CreateAdminUserHandler(
            $this->adminUserRepository,
            $this->adminUserFactory,
            $this->canonicalizer,
            $this->validator,
            self::GROUPS,
        );
    }

    private function createAdminUserMessage(
        bool $administrationAccess = true,
        bool $apiAccess = false,
    ): CreateAdminUser {
        return new CreateAdminUser(
            self::NON_CANONICALIZED_EMAIL,
            self::USERNAME,
            self::FIRST_NAME,
            self::LAST_NAME,
            self::PASSWORD,
            self::LOCALE_CODE,
            self::ENABLED,
            $administrationAccess,
            $apiAccess,
        );
    }
}
