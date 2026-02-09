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

namespace Tests\Sylius\Component\User\Model;

use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;

final class UserTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        $this->user = new User();
    }

    public function testShouldImplementUserInterface(): void
    {
        $this->assertInstanceOf(UserInterface::class, $this->user);
    }

    public function testShouldBeNotVerifiedByDefault(): void
    {
        $this->assertFalse($this->user->isVerified());
    }

    public function testShouldVerifiedAtDateBeMutable(): void
    {
        $date = new DateTime();

        $this->user->setVerifiedAt($date);

        $this->assertSame($date, $this->user->getVerifiedAt());
    }

    public function testShouldBeVerifiedWhenVerifiedAtIsNotEmpty(): void
    {
        $date = new DateTime();

        $this->user->setVerifiedAt($date);

        $this->assertTrue($this->user->isVerified());
    }

    public function testShouldHaveNoPasswordRequestedAtDateByDefault(): void
    {
        $this->assertNull($this->user->getPasswordRequestedAt());
    }

    public function testShouldPasswordRequestAtDateBeMutable(): void
    {
        $date = new DateTime();

        $this->user->setPasswordRequestedAt($date);

        $this->assertSame($date, $this->user->getPasswordRequestedAt());
    }

    public function testShouldReturnTrueIfPasswordRequestIsNonExpired(): void
    {
        $this->user->setPasswordRequestedAt(new DateTime('-1 hour'));

        $this->assertTrue($this->user->isPasswordRequestNonExpired(new DateInterval('P1D')));
    }

    public function testShouldReturnFalseIfPasswordRequestIsExpired(): void
    {
        $this->user->setPasswordRequestedAt(new DateTime('-2 hour'));

        $this->assertFalse($this->user->isPasswordRequestNonExpired(new DateInterval('PT1H')));
    }

    public function testShouldHaveEmailAndEmailCanonical(): void
    {
        $this->user->setEmail('admin@example.com');
        $this->user->setEmailCanonical('user@example.com');

        $this->assertSame('admin@example.com', $this->user->getEmail());
        $this->assertSame('user@example.com', $this->user->getEmailCanonical());
    }
}
