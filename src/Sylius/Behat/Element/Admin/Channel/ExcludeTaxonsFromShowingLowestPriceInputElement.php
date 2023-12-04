<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Element\Admin\Channel;

use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Component\Core\Model\TaxonInterface;

final class ExcludeTaxonsFromShowingLowestPriceInputElement extends Element implements ExcludeTaxonsFromShowingLowestPriceInputElementInterface
{
    public function excludeTaxon(TaxonInterface $taxon): void
    {
        $excludeTaxonElement = $this->getElement('taxons_excluded_from_showing_lowest_price')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $excludeTaxonElement, $taxon->getName());
    }

    public function removeExcludedTaxon(TaxonInterface $taxon): void
    {
        $excludeTaxonElement = $this->getElement('taxons_excluded_from_showing_lowest_price')->getParent();

        AutocompleteHelper::removeValue($this->getSession(), $excludeTaxonElement, $taxon->getName());
    }

    public function hasTaxonExcluded(TaxonInterface $taxon): bool
    {
        $excludedTaxons = $this->getElement('taxons_excluded_from_showing_lowest_price')->getValue();

        return str_contains($excludedTaxons, $taxon->getCode());
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'taxons_excluded_from_showing_lowest_price' => '#sylius_channel_channelPriceHistoryConfig_taxonsExcludedFromShowingLowestPrice',
        ]);
    }
}
