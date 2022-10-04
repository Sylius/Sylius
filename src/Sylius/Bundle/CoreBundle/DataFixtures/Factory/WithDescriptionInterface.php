<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithDescriptionInterface
{
    /**
     * @return $this
     */
    public function withDescription(string $description): self;
}
