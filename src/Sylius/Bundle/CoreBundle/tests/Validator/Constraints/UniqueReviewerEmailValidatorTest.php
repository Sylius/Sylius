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
use Sylius\Bundle\CoreBundle\Validator\Constraints\UniqueReviewerEmail;
use Sylius\Bundle\CoreBundle\Validator\Constraints\UniqueReviewerEmailValidator;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class UniqueReviewerEmailValidatorTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private MockObject&TokenStorageInterface $tokenStorage;

    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private ExecutionContextInterface&MockObject $executionContext;

    private UniqueReviewerEmailValidator $validator;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new UniqueReviewerEmailValidator(
            $this->userRepository,
            $this->tokenStorage,
            $this->authorizationChecker,
        );

        $this->validator->initialize($this->executionContext);
    }

    public function testExtendsConstraintValidatorClass(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testValidatesIfUserWithGivenEmailIsAlreadyRegistered(): void
    {
        $constraint = new UniqueReviewerEmail();
        $token = $this->createMock(TokenInterface::class);
        $review = $this->createMock(ReviewInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $existingUser = $this->createMock(UserInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $review->method('getAuthor')->willReturn($customer);
        $customer->method('getEmail')->willReturn('john.doe@example.com');

        $this->tokenStorage->method('getToken')->willReturn($token);
        $this->authorizationChecker->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->willReturn(false);
        $this->userRepository->method('findOneByEmail')->with('john.doe@example.com')->willReturn($existingUser);

        $this->executionContext->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())
            ->method('atPath')
            ->with('author')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($review, $constraint);
    }

    public function testBuildsViolationWhenReviewAlreadyHasARegisteredEmailAndNoCurrentUser(): void
    {
        $constraint = new UniqueReviewerEmail();
        $review = $this->createMock(ReviewInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $existingUser = $this->createMock(UserInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->tokenStorage->method('getToken')->willReturn(null);
        $review->method('getAuthor')->willReturn($customer);
        $customer->method('getEmail')->willReturn('john.doe@example.com');
        $this->userRepository->method('findOneByEmail')->with('john.doe@example.com')->willReturn($existingUser);

        $this->executionContext->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())
            ->method('atPath')
            ->with('author')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($review, $constraint);
    }
}
