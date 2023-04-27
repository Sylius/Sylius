<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait EnabledTrait
{
    public function enabled(): self
    {
        return $this->addState(['enabled' => true]);
    }
}
