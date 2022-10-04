<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithLocalesInterface
{
    /**
     * @return $this
     */
    public function withLocales(array $locales): self;
}
