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

namespace Sylius\Behat\Page\Admin\Product\Common;

use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

trait ProductTaxonomyTrait
{
    public function getDefinedProductTaxonomyElements(): array
    {
        return [
            'main_taxon' => '#sylius_product_mainTaxon',
        ];
    }

    public function selectMainTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $mainTaxonElement, $taxon->getName());
    }

    public function hasMainTaxonWithName(string $taxonName): bool
    {
        $this->openTaxonBookmarks();
        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        return $taxonName === $mainTaxonElement->find('css', '.search > .text')->getText();
    }

    public function checkProductTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $productTaxonsCodes = [];
        $productTaxonsElement = $this->getElement('product_taxons');
        if ($productTaxonsElement->getValue() !== '') {
            $productTaxonsCodes = explode(',', $productTaxonsElement->getValue());
        }
        $productTaxonsCodes[] = $taxon->getCode();

        $productTaxonsElement->setValue(implode(',', $productTaxonsCodes));
    }

    public function selectProductTaxon(TaxonInterface $taxon): void
    {
        $productTaxonsCodes = [];
        $productTaxonsElement = $this->getElement('product_taxons');
        if ($productTaxonsElement->getValue() !== '') {
            $productTaxonsCodes = explode(',', $productTaxonsElement->getValue());
        }
        $productTaxonsCodes[] = $taxon->getCode();

        $productTaxonsElement->setValue(implode(',', $productTaxonsCodes));
    }

    public function unselectProductTaxon(TaxonInterface $taxon): void
    {
        $productTaxonsCodes = [];
        $productTaxonsElement = $this->getElement('product_taxons');
        if ($productTaxonsElement->getValue() !== '') {
            $productTaxonsCodes = explode(',', $productTaxonsElement->getValue());
        }

        $key = array_search($taxon->getCode(), $productTaxonsCodes);
        if ($key !== false) {
            unset($productTaxonsCodes[$key]);
        }

        $productTaxonsElement->setValue(implode(',', $productTaxonsCodes));
    }

    public function hasMainTaxon(): bool
    {
        $this->openTaxonBookmarks();

        return $this->getDocument()->find('css', '.search > .text')->getText() !== '';
    }

    public function isTaxonVisibleInMainTaxonList(string $taxonName): bool
    {
        $this->openTaxonBookmarks();

        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        return AutocompleteHelper::isValueVisible($this->getSession(), $mainTaxonElement, $taxonName);
    }

    public function isTaxonChosen(string $taxonName): bool
    {
        $productTaxonsElement = $this->getElement('product_taxons');

        $taxonName = strtolower(str_replace('-', '_', $taxonName));

        return str_contains($productTaxonsElement->getValue(), $taxonName);
    }

    private function openTaxonBookmarks(): void
    {
        $this->getElement('taxonomy')->click();
    }
}
