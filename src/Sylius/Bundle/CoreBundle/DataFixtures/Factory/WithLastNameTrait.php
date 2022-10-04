<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithLastNameTrait
{
    public function withLastName(string $lastName): self
    {
        return $this->addState(['last_name' => $lastName]);
    }
}
