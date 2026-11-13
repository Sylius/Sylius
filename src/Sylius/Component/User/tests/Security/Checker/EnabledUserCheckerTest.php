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

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\Checker\EnabledUserChecker;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AllowMockObjectsWithoutExpectations]
final class EnabledUserCheckerTest extends TestCase
{
    /** @var UserInterface&MockObject */
    private MockObject $userMock;

    private EnabledUserChecker $userChecker;

    protected function setUp(): void
    {
        $this->userMock = $this->createMock(UserInterface::class);
        $this->userChecker = new EnabledUserChecker();
    }

    public function testShouldThrowDisabledExceptionIfAccountIsDisabled(): void
    {
        $this->expectException(DisabledException::class);

        $this->userMock->expects($this->once())->method('isEnabled')->willReturn(false);

        $this->userChecker->checkPreAuth($this->userMock);
        $this->userChecker->checkPostAuth($this->userMock);
    }

    public function testShouldDoNothingIfUserIsEnabled(): void
    {
        $this->userMock->expects($this->once())->method('isEnabled')->willReturn(true);

        $this->userChecker->checkPreAuth($this->userMock);
    }

    public function testShouldThrowCustomUserMessageAccountStatusExceptionWithTranslatedMessageIfTranslatorIsGivenAndAccountIsDisabled(): void
    {
        /** @var TranslatorInterface&MockObject $translatorMock */
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock->expects($this->once())
            ->method('trans')
            ->with('sylius.user.account_disabled', [], 'validators')
            ->willReturn('Translated disabled account message.')
        ;

        $userChecker = new EnabledUserChecker($translatorMock);

        $this->userMock->expects($this->once())->method('isEnabled')->willReturn(false);

        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('Translated disabled account message.');

        $userChecker->checkPreAuth($this->userMock);
    }
}
