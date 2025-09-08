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

namespace Tests\Sylius\Component\User\Security\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\Checker\TokenUniquenessChecker;
use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class TokenUniquenessCheckerTest extends TestCase
{
    /** @var RepositoryInterface<UserInterface>&MockObject */
    private MockObject $userRepository;

    private TokenUniquenessChecker $tokenUniquenessChecker;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(RepositoryInterface::class);
        $this->tokenUniquenessChecker = new TokenUniquenessChecker($this->userRepository, 'aRandomToken');
    }

    public function testShouldImplementTokenUniquenessCheckerInterface(): void
    {
        $this->assertInstanceOf(UniquenessCheckerInterface::class, $this->tokenUniquenessChecker);
    }

    public function testShouldReturnTrueWhenTokenIsNotUsed(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['aRandomToken' => 'freeToken'])
            ->willReturn(null);

        $this->assertTrue($this->tokenUniquenessChecker->isUnique('freeToken'));
    }

    public function testShouldReturnFalseWhenTokenIsInUse(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['aRandomToken' => 'takenToken'])
            ->willReturn($this->createMock(UserInterface::class));

        $this->assertFalse($this->tokenUniquenessChecker->isUnique('takenToken'));
    }
}
