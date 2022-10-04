<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

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
