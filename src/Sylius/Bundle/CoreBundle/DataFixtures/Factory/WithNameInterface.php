<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithNameInterface
{
    public function withName(string $name): self;
}
