<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Product;

use Sylius\Component\Product\Model\OptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function addToCart()
    {
        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function addToCartWithQuantity($quantity)
    {
        $this->getDocument()->fillField('Quantity', $quantity);
        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function addToCartWithVariant($variant)
    {
        $item = $this->getDocument()->find('css', sprintf('#sylius-product-variants tbody tr:contains("%s")', $variant));
        $radio = $item->find('css', 'input');

        $this->getDocument()->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));

        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function addToCartWithOption(OptionInterface $option, $optionValue)
    {
        $select = $this->getDocument()->find('css', sprintf('select#sylius_cart_item_variant_%s', $option->getCode()));

        $this->getDocument()->selectFieldOption($select->getAttribute('name'), $optionValue);
        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function visit($url)
    {
        $absoluteUrl = $this->makePathAbsolute($url);
        $this->getDriver()->visit($absoluteUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getElement('name')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttributeWithValue($name, $value)
    {
        $tableWithAttributes = $this->getElement('attributes');

        $nameTdSelector = sprintf('tr > td.sylius-product-attribute-name:contains("%s")', $name);
        $nameTd = $tableWithAttributes->find('css', $nameTdSelector);

        if (null === $nameTd) {
            return false;
        }

        $row = $nameTd->getParent();

        return $value === $row->find('css', 'td.sylius-product-attribute-value')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->getElement('product_price')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function isOutOfStock()
    {
        return $this->hasElement('out-of-stock');
    }

    /**
     * {@inheritdoc}
     */
    public function hasAddToCartButton()
    {
        return $this->getDocument()->hasButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_product_show';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'attributes' => '#sylius-product-attributes',
            'name' => '#sylius-product-name',
            'out-of-stock' => '#sylius-product-out-of-stock',
            'product_price' => '#product-price'
        ]);
    }
}
