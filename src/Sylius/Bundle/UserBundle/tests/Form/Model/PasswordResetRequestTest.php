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
use Sylius\Bundle\UserBundle\Form\Model\PasswordResetRequest;

final class PasswordResetRequestTest extends TestCase
{
    private PasswordResetRequest $passwordResetRequest;

    protected function setUp(): void
    {
        $this->passwordResetRequest = new PasswordResetRequest();
    }

    public function testHasEmail(): void
    {
        $this->passwordResetRequest->setEmail('test@example.com');

        $this->assertSame('test@example.com', $this->passwordResetRequest->getEmail());
    }
}
