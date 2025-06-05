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
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;

final class ChangePasswordTest extends TestCase
{
    private ChangePassword $changePassword;

    protected function setUp(): void
    {
        $this->changePassword = new ChangePassword();
    }

    public function testHasCurrentPassword(): void
    {
        $this->changePassword->setCurrentPassword('testPassword');

        $this->assertSame('testPassword', $this->changePassword->getCurrentPassword());
    }

    public function testHasNewPassword(): void
    {
        $this->changePassword->setNewPassword('testPassword');

        $this->assertSame('testPassword', $this->changePassword->getNewPassword());
    }
}
