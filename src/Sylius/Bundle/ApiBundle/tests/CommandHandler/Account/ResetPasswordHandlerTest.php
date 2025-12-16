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
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\ResetPasswordHandler;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetterInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class ResetPasswordHandlerTest extends TestCase
{
    private MockObject&UserPasswordResetterInterface $userPasswordResetter;

    private ResetPasswordHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->userPasswordResetter = $this->createMock(UserPasswordResetterInterface::class);
        $this->handler = new ResetPasswordHandler($this->userPasswordResetter);
    }

    public function testDelegatesPasswordResetting(): void
    {
        $this->userPasswordResetter->expects(self::once())
            ->method('reset')
            ->with('TOKEN', 'newPassword');
        $this->handler->__invoke(
            new ResetPassword(
                'TOKEN',
                'newPassword',
                'newPassword',
            ),
        );
    }
}
