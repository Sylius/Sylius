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

namespace Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as CrudIndexPage;
use Webmozart\Assert\Assert;

class IndexPerTaxonPage extends CrudIndexPage implements IndexPerTaxonPageInterface
{
    public function getProductPosition(string $productName): int
    {
        return (int) $this->getElement('product_taxon_position', ['%name%' => $productName])->getValue();
    }

    public function hasProductsInOrder(array $productNames): bool
    {
        $productsOnPage = $this->getColumnFields('name');

        foreach ($productsOnPage as $key => $product) {
            if ($productNames[$key] !== $product) {
                return false;
            }
        }

        return true;
    }

    public function setPositionOfProduct(string $productName, string $position): void
    {
        /** @var NodeElement $productsRow */
        $productsRow = $this->getElement('table')->find('css', sprintf('tbody > tr:contains("%s")', $productName));
        Assert::notNull($productsRow, 'There are no row with given product\'s name!');

        $productsRow->find('css', '.sylius-product-taxon-position')->setValue($position);
    }

    public function savePositions(): void
    {
        $saveConfigurationButton = $this->getElement('save_configuration_button');
        $saveConfigurationButton->press();

        $this->getDocument()->waitFor(5, fn () => null === $saveConfigurationButton->find('css', '.loading'));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'product_taxon_position' => '.sylius-product-taxon-position[data-test-product-name="%name%"]',
            'save_configuration_button' => '.sylius-save-position',
        ]);
    }
}
