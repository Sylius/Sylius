<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

interface WithNameInterface
{
    /**
     * @return $this
     */
    public function withName(string $name): self;
}
