<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Command;

interface CreateManyInterface
{
    public function min(): int;

    public function max(): ?int;
}
