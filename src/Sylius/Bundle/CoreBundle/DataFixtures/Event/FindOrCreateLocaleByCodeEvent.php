<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateLocaleByCodeEvent extends Event
{
    private Proxy|LocaleInterface|null $locale = null;

    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLocale(): Proxy|LocaleInterface
    {
        Assert::notNull($this->locale, sprintf('Locale "%s" has not been found or created.', $this->code));

        return $this->locale;
    }

    public function setLocale(Proxy|LocaleInterface $locale): void
    {
        $this->locale = $locale;
    }
}
