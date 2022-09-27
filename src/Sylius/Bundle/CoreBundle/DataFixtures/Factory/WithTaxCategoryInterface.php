<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Zenstruck\Foundry\Proxy;

interface WithTaxCategoryInterface
{
    public function withTaxCategory(Proxy|TaxCategoryInterface|string $taxCategory): static;
}
