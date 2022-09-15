<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithFirstNameInterface
{
    public function withFirstName(string $firstName): self;
}
