<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithDescriptionTrait
{
    public function withDescription(string $description): static
    {
        return $this->addState(['description' => $description]);
    }
}
