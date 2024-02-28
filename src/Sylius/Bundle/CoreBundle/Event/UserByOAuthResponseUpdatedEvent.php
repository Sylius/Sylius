<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Event;

use Sylius\Component\Core\Model\ShopUserInterface;

final class UserByOAuthResponseUpdatedEvent
{
    public function __construct(private ShopUserInterface $user)
    {
    }

    public function getUser(): ShopUserInterface
    {
        return $this->user;
    }
}
