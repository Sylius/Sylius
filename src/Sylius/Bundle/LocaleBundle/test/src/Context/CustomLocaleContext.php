<?php

declare(strict_types=1);

namespace Sylius\Bundle\LocaleBundle\Application\Context;

use Sylius\Component\Locale\Context\LocaleContextInterface;

final class CustomLocaleContext implements LocaleContextInterface
{
    public function getLocaleCode(): string
    {
        return 'de_DE';
    }
}
