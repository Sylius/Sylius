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

namespace Tests\Sylius\Bundle\UserBundle\Form\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;

final class PasswordResetTest extends TestCase
{
    private PasswordReset $passwordReset;

    protected function setUp(): void
    {
        $this->passwordReset = new PasswordReset();
    }

    public function testHasNewPassword(): void
    {
        $this->passwordReset->setPassword('testPassword');

        $this->assertSame('testPassword', $this->passwordReset->getPassword());
    }
}
