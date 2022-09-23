<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @mixin ModelFactory
 */
trait WithTaxCategoryTrait
{
    public function withTaxCategory(Proxy|TaxCategoryInterface|string $taxCategory): self
    {
        return $this->addState(['tax_category' => $taxCategory]);
    }
}
