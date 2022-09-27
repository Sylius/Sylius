<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithLocalesInterface
{
    public function withLocales(array $locales): static;
}
