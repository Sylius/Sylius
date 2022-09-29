<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithTaxaInterface
{
    public function withTaxa(array $taxa): static;
}
