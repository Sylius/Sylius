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

namespace Tests\Sylius\Bundle\CoreBundle\OAuth\Exception;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OAuth\Exception\UnverifiedEmailException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final class UnverifiedEmailExceptionTest extends TestCase
{
    public function testExposesATranslatableMessageNamingTheResourceOwner(): void
    {
        $exception = new UnverifiedEmailException('google');

        $this->assertSame(UnverifiedEmailException::MESSAGE_KEY, $exception->getMessageKey());
        $this->assertSame(['%resource_owner%' => 'google'], $exception->getMessageData());
    }

    /**
     * Symfony's AuthenticatorManager replaces every UserNotFoundException with a generic BadCredentialsException,
     * which would discard the message above before it reaches the login page.
     */
    public function testIsNotAUserNotFoundException(): void
    {
        $this->assertNotInstanceOf(UserNotFoundException::class, new UnverifiedEmailException('google'));
    }

    public function testSurvivesSerialization(): void
    {
        $exception = unserialize(serialize(new UnverifiedEmailException('google')));

        $this->assertInstanceOf(UnverifiedEmailException::class, $exception);
        $this->assertSame(UnverifiedEmailException::MESSAGE_KEY, $exception->getMessageKey());
        $this->assertSame(['%resource_owner%' => 'google'], $exception->getMessageData());
    }
}
