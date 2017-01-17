<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as CrudIndexPage;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class IndexPerTaxonPage extends CrudIndexPage implements IndexPerTaxonPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasProductsInOrder(array $productNames)
    {
        $productsOnPage = $this->getColumnFields('name');

        foreach ($productsOnPage as $key => $product) {
            if ($productNames[$key] !== $product) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setPositionOfProduct($productName, $position)
    {
        /** @var NodeElement $productsRow */
        $productsRow = $this->getElement('table')->find('css', sprintf('tbody > tr:contains("%s")', $productName));
        Assert::notNull($productsRow, 'There are no row with given product\'s name!');

        $productsRow->find('css', '.sylius-product-taxon-position')->setValue($position);
    }

    public function savePositions()
    {
        $this->getElement('save_configuration_button')->press();

        $this->getDocument()->waitFor(5, function () {
            return false === $this->getElement('save_configuration_button')->hasClass('loading');
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'save_configuration_button' => '.sylius-save-position',
        ]);
    }
}
