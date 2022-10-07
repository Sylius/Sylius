<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class CreateDefaultLocaleEvent extends Event
{
    private ?Proxy $locale = null;

    public function getLocale(): Proxy
    {
        Assert::notNull($this->locale, 'Locale has been created.');

        return $this->locale;
    }

    public function setLocale(Proxy $locale): void
    {
        $this->locale = $locale;
    }
}
