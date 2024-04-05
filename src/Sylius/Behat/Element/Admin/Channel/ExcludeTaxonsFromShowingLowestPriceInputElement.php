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

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ExcludeTaxonsFromShowingLowestPriceInputElement extends Element implements ExcludeTaxonsFromShowingLowestPriceInputElementInterface
{
    public function __construct(
        Session $session,
        MinkParameters|array $minkParameters = [],
        private AutocompleteHelperInterface $autocompleteHelper, // TODO: make it static fcs //
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function excludeTaxon(TaxonInterface $taxon): void
    {
        $excludeTaxonElement = $this->getElement('taxons_excluded_from_showing_lowest_price')->getParent();

        $this->autocompleteHelper->select($this->getDriver(), $excludeTaxonElement->getXpath(), $taxon->getName());
    }

    public function removeExcludedTaxon(TaxonInterface $taxon): void
    {
        $excludeTaxonElement = $this->getElement('taxons_excluded_from_showing_lowest_price')->getParent();

        $this->autocompleteHelper->remove($this->getDriver(), $excludeTaxonElement->getXpath(), $taxon->getName());
    }

    public function hasTaxonExcluded(TaxonInterface $taxon): bool
    {
        $code = $taxon->getCode();
        Assert::notNull($code);

        $excludedTaxons = $this->getElement('taxons_excluded_from_showing_lowest_price')->getParent();

        return in_array(
            $code,
            $this->autocompleteHelper->getSelectedItems($this->getDriver(), $excludedTaxons->getXpath()),
        );
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'taxons_excluded_from_showing_lowest_price' => '#sylius_channel_channelPriceHistoryConfig_taxonsExcludedFromShowingLowestPrice',
        ]);
    }
}
