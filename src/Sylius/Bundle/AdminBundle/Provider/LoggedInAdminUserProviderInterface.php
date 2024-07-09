<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Component\Core\Model\AdminUserInterface;

interface LoggedInAdminUserProviderInterface
{
    public function hasUser(): bool;

    public function getUser(): ?AdminUserInterface;
}
