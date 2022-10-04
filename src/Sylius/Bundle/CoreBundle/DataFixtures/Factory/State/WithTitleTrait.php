<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithTitleTrait
{
    public function withTitle(string $title): self
    {
        return $this->addState(['title' => $title]);
    }
}
