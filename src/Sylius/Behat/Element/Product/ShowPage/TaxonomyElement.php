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

namespace Sylius\Behat\Element\Product\ShowPage;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;

final class TaxonomyElement extends Element implements TaxonomyElementInterface
{
    public function getProductMainTaxon(): string
    {
        return $this->getElement('main_taxon')->getText();
    }

    public function hasProductTaxon(string $taxonName): bool
    {
        $taxons = $this->getElement('product_taxon');

        /** @var NodeElement $taxon */
        foreach ($taxons->findAll('css', 'li') as $taxon) {
            if ($taxon->getText() === $taxonName) {
                return true;
            }
        }

        return false;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'main_taxon' => '#taxonomy tr:contains("Main taxon") td:nth-child(2)',
            'product_taxon' => '#taxonomy tr:contains("Product taxon") td:nth-child(2)',
        ]);
    }
}
