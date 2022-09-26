<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithPriorityInterface
{
    public function withPriority(int $priority): self;
}
