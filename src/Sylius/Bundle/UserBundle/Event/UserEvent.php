<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Event;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    public function __construct(private UserInterface $user)
    {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
