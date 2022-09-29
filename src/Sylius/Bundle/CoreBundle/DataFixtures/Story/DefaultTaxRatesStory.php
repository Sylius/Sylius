<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxRateFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultTaxRatesStory extends Story implements DefaultTaxRatesStoryInterface
{
    public function __construct(private TaxRateFactoryInterface $taxRateFactory) {
    }

    public function build(): void
    {
        $this->taxRateFactory::new()
            ->withCode('clothing_sales_tax_7')
            ->withName('Clothing Sales Tax 7%')
            ->withZone('US')
            ->withCategory('clothing')
            ->withAmount(0.07)
            ->create()
        ;

        $this->taxRateFactory::new()
            ->withCode('sales_tax_20')
            ->withName('Sales Tax 20%')
            ->withZone('US')
            ->withCategory('other')
            ->withAmount(0.2)
            ->create()
        ;
    }
}
