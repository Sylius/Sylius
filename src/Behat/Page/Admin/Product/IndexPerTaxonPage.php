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
        /** @var NodeElement $productsRow */
        $productsRow = $this->getElement('table')->find('css', sprintf('tbody > tr:contains("%s")', $productName));
        Assert::notNull($productsRow, 'There are no row with given product\'s name!');

        return (int) $productsRow->find('css', '.sylius-product-taxon-position')->getValue();
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

    public function filterByName(string $name): void
    {
        $this->getElement('name_filter')->setValue($name);
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'name_filter' => '#criteria_search_value',
            'save_configuration_button' => '[data-test-save-configuration-button]',
        ]);
    }
}
