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

namespace Tests\Sylius\Bundle\UserBundle\Event;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\User\Model\UserInterface;

final class UserEventTest extends TestCase
{
    private MockObject&UserInterface $user;

    private UserEvent $userEvent;

    protected function setUp(): void
    {
        $this->user = $this->createMock(UserInterface::class);

        $this->userEvent = new UserEvent($this->user);
    }

    public function testHasUser(): void
    {
        $this->assertSame($this->user, $this->userEvent->getUser());
    }
}
