<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithEmailInterface
{
    /**
     * @return $this
     */
    public function withEmail(string $email): self;
}
