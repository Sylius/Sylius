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

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class TaxonomyElement extends Element implements TaxonomyElementInterface
{
    public function getProductMainTaxon(): string
    {
        return $this->getElement('main_taxon')->getText();
    }

    public function getProductTaxons(): string
    {
        return $this->getElement('product_taxons')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'main_taxon' => '[data-test-main-taxon]',
            'product_taxons' => '[data-test-product-taxons]',
        ]);
    }
}
