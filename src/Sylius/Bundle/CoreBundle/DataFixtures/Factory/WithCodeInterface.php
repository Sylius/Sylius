<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithCodeInterface
{
    public function withCode(string $code): static;
}
