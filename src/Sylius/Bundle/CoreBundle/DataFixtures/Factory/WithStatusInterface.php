<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithStatusInterface
{
    public function withStatus(string $status): static;
}
