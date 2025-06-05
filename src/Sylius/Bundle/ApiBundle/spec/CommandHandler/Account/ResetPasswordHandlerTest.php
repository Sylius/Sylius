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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\ResetPasswordHandler;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetterInterface;

final class ResetPasswordHandlerTest extends TestCase
{
    /** @var UserPasswordResetterInterface|MockObject */
    private MockObject $userPasswordResetterMock;

    private ResetPasswordHandler $resetPasswordHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->userPasswordResetterMock = $this->createMock(UserPasswordResetterInterface::class);
        $this->resetPasswordHandler = new ResetPasswordHandler($this->userPasswordResetterMock);
    }

    public function testDelegatesPasswordResetting(): void
    {
        $this->userPasswordResetterMock->expects(self::once())->method('reset')->with('TOKEN', 'newPassword');
        $this(new ResetPassword('TOKEN', 'newPassword', 'newPassword'));
    }
}
