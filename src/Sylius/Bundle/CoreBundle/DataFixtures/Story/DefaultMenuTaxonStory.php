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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultMenuTaxonStory extends Story implements DefaultMenuTaxonStoryInterface
{
    public function __construct(private TaxonFactoryInterface $taxonFactory)
    {
    }

    public function build(): void
    {
        $this->taxonFactory::new()
            ->withCode('MENU_CATEGORY')
            ->withName('Category')
            ->withTranslations([
                'en_US' => ['name' => 'Category'],
                'fr_FR' => ['name' => 'Catégorie'],
            ])
            ->create()
        ;
    }
}
