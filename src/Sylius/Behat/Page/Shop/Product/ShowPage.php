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
        $item = $this->getDocument()->find('css', sprintf('#product-variants tbody tr:contains("%s")', $variant));
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
    public function isAttributeWithValueOnPage($attributeName, $attributeValue)
    {
        return (
            $this->isAttributeWith($attributeName, 'tbody > tr > td[id=sylius_attribute_name]') &&
            $this->isAttributeWith($attributeValue, 'tbody > tr > td[id=sylius_attribute_value]')
        );
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
            'name' => '#sylius_product_name',
            'attributes' => '#product-attributes'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    private function isAttributeWith($fieldValue, $selector)
    {
        $rows = $this->getElement('attributes')->findAll('css', $selector);
        foreach($rows as $row) {
            if($fieldValue === $row->getText()) {
                return true;
            }
        }

        return false;
    }
}
