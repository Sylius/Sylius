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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultTaxCategoriesStory extends Story implements DefaultTaxCategoriesStoryInterface
{
    public function __construct(private TaxCategoryFactoryInterface $taxCategoryFactory) {
    }

    public function build(): void
    {
        $this->taxCategoryFactory::new()
            ->withCode('clothing')
            ->withName('Clothing')
            ->create()
        ;

        $this->taxCategoryFactory::new()
            ->withCode('other')
            ->withName('Other')
            ->create()
        ;
    }
}
