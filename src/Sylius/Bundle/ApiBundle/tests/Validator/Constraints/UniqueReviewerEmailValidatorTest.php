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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueReviewerEmail;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueReviewerEmailValidator;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UniqueReviewerEmailValidatorTest extends TestCase
{
    private MockObject&UserRepositoryInterface $shopUserRepository;

    private MockObject&UserContextInterface $userContext;

    private ExecutionContextInterface&MockObject $executionContext;

    private UniqueReviewerEmailValidator $uniqueReviewerEmailValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->uniqueReviewerEmailValidator = new UniqueReviewerEmailValidator($this->shopUserRepository, $this->userContext);
        $this->uniqueReviewerEmailValidator->initialize($this->executionContext);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->uniqueReviewerEmailValidator);
    }

    public function testAddsViolationIfUserWithGivenEmailIsAlreadyRegistered(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->userContext->expects(self::once())->method('getUser')->willReturn(null);
        $this->shopUserRepository->expects(self::once())->method('findOneByEmail')->with('email@example.com')->willReturn($shopUserMock);
        $this->executionContext->expects(self::once())->method('addViolation')->with('sylius.review.author.already_exists');
        $this->uniqueReviewerEmailValidator->validate('email@example.com', new UniqueReviewerEmail());
    }

    public function testDoesNothingIfValueIsNull(): void
    {
        $this->executionContext->expects(self::never())->method('addViolation');
        $this->uniqueReviewerEmailValidator->validate(null, new UniqueReviewerEmail());
    }

    public function testThrowsAnExceptionIfConstraintIsNotOfExpectedType(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->uniqueReviewerEmailValidator->validate(
            '',
            $this->createMock(Constraint::class),
        );
    }

    public function testDoesNotAddViolationIfTheGivenEmailIsTheSameAsLoggedInShopUser(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->userContext->expects(self::once())->method('getUser')->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('getEmail')->willReturn('email@example.com');
        $this->executionContext->expects(self::never())->method('addViolation');
        $this->uniqueReviewerEmailValidator->validate('email@example.com', new UniqueReviewerEmail());
    }
}
