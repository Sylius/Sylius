<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Component\User\Model\UserInterface;

interface LoggedInUserProviderInterface
{
    public function hasUser(): bool;

    public function getUser(): ?UserInterface;
}
