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

namespace Tests\Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\ResetPassword;
use Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\ResetPasswordHandler;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetterInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final class ResetPasswordHandlerTest extends TestCase
{
    private MockObject&UserPasswordResetterInterface $userPasswordResetter;

    private ResetPasswordHandler $handler;

    protected function setUp(): void
    {
        $this->userPasswordResetter = $this->createMock(UserPasswordResetterInterface::class);
        $this->handler = new ResetPasswordHandler($this->userPasswordResetter);
    }

    public function testItIsAMessageHandler(): void
    {
        $reflection = new \ReflectionClass(ResetPasswordHandler::class);
        $attributes = $reflection->getAttributes(AsMessageHandler::class);

        $this->assertCount(1, $attributes);
    }

    public function testItDelegatesPasswordResetting(): void
    {
        $this->userPasswordResetter
            ->expects($this->once())
            ->method('reset')
            ->with('TOKEN', 'newPassword')
        ;

        ($this->handler)(new ResetPassword('TOKEN', 'newPassword'));
    }
}
