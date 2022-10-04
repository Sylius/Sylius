<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithTaxaInterface
{
    /**
     * @return $this
     */
    public function withTaxa(array $taxa): self;
}
