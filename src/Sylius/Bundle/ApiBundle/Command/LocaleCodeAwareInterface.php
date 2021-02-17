<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Command;

interface LocaleCodeAwareInterface extends CommandAwareDataTransformerInterface
{
    public function getLocaleCode(): ?string;

    public function setLocaleCode(?string $localeCode): void;
}
