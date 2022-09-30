<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithTitleInterface
{
    public function withTitle(string $title): static;
}
