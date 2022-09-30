<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithTitleTrait
{
    public function withTitle(string $title): static
    {
        return $this->addState(['title' => $title]);
    }
}
