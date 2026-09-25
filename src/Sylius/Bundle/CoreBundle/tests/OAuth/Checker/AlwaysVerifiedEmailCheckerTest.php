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

namespace Tests\Sylius\Bundle\CoreBundle\OAuth\Checker;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OAuth\Checker\AlwaysVerifiedEmailChecker;
use Sylius\Bundle\CoreBundle\OAuth\Checker\EmailVerificationCheckerInterface;

final class AlwaysVerifiedEmailCheckerTest extends TestCase
{
    private AlwaysVerifiedEmailChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new AlwaysVerifiedEmailChecker();
    }

    public function testImplementsEmailVerificationCheckerInterface(): void
    {
        $this->assertInstanceOf(EmailVerificationCheckerInterface::class, $this->checker);
    }

    public function testConsidersEveryEmailAddressVerified(): void
    {
        $this->assertTrue($this->checker->isEmailVerified($this->createMock(UserResponseInterface::class)));
    }
}
