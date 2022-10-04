<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithLastNameInterface
{
    /**
     * @return $this
     */
    public function withLastName(string $lastName): self;
}
