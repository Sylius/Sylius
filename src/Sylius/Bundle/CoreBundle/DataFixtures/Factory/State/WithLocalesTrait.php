<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithLocalesTrait
{
    public function withLocales(array $locales): self
    {
        return $this->addState(['locales' => $locales]);
    }
}
