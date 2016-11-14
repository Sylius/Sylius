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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Sylius\Behat\Page\Admin\Crud\IndexPage as CrudIndexPage;
use Sylius\Component\Core\Model\ProductInterface;
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
            if($productNames[$key] !== $product) {
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

        $productsRow->find('css', 'button:contains("Change position")')->press();

        $productTaxonId = $productsRow->find('css', '.position')->getAttribute('data-id');

        $this->getElement('position_input_field', ['%product_taxon_id%' => $productTaxonId])->setValue($position);

        $this->getDocument()->waitFor(5, function () use ($productTaxonId){
            return 'hidden' === $this->getDocument()->find('css', sprintf('input[data-id=%s]', $productTaxonId))->getAttribute('type');
        });
    }

    /**
     * {@inheritDoc}
     */
    public function insertBefore(ProductInterface $draggableProduct, ProductInterface $targetProduct)
    {
        $seleniumDriver = $this->getSeleniumDriver();
        $draggableProductLocator = sprintf('.item:contains("%s")', $draggableProduct->getName());
        $targetProductLocator = sprintf('.item:contains("%s")', $targetProduct->getName());

        $script = <<<JS
(function ($) {
    $('$draggableProductLocator').simulate('drag-n-drop',{
        dragTarget: $('$targetProductLocator'),
        interpolation: {stepWidth: 10, stepDelay: 30} 
    });    
})(jQuery);
JS;

        $seleniumDriver->executeScript($script);
        $this->getDocument()->waitFor(5, function () {
            return false;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return sprintf('sylius_admin_product_index_per_taxon');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'table' => '.table',
            'change_position_button' => 'button:contains("Change position")',
            'position_input_field' => 'input[data-id="%product_taxon_id%"]'
        ]);
    }

    /**
     * @return Selenium2Driver
     *
     * @throws UnsupportedDriverActionException
     */
    private function getSeleniumDriver()
    {
        /** @var Selenium2Driver $driver */
        $driver = $this->getDriver();
        if (!$driver instanceof Selenium2Driver) {
            throw new UnsupportedDriverActionException('This action is not supported by %s', $driver);
        }

        return $driver;
    }
}
