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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
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
    public function addToCart()
    {
        $this->getDocument()->pressButton('Add to cart');

        if ($this->getDriver() instanceof Selenium2Driver) {
            sleep(3); #TODO Kopytko
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addToCartWithQuantity($quantity)
    {
        $this->getDocument()->fillField('Quantity', $quantity);
        $this->getDocument()->pressButton('Add to cart');

        if ($this->getDriver() instanceof Selenium2Driver) {
            sleep(3); #TODO Kopytko
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addToCartWithVariant($variant)
    {
        $this->selectVariant($variant);

        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function addToCartWithOption(ProductOptionInterface $option, $optionValue)
    {
        $select = $this->getDocument()->find('css', sprintf('select#sylius_add_to_cart_cartItem_variant_%s', $option->getCode()));

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
    public function getAttributeByName($name)
    {
        $attributesTable = $this->getElement('attributes');

        $nameTdSelector = sprintf('tr > td.sylius-product-attribute-name:contains("%s")', $name);
        $nameTd = $attributesTable->find('css', $nameTdSelector);

        if (null === $nameTd) {
            return false;
        }

        $row = $nameTd->getParent();

        return trim($row->find('css', 'td.sylius-product-attribute-value')->getText());
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        $attributesTable = $this->getElement('attributes');

        return $attributesTable->findAll('css', 'tr > td.sylius-product-attribute-name');
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product)
    {
        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        if (!$this->hasElement('validation_errors')) {
            return false;
        }

        return $this->getElement('validation_errors')->getText() === $message;
    }

    /**
     * {@inheritdoc}
     */
    public function waitForValidationErrors($timeout)
    {
        $errorsContainer = $this->getElement('selecting_variants');

        $this->getDocument()->waitFor($timeout, function () use ($errorsContainer) {
            return false !== $errorsContainer->has('css', '[class ~="sylius-validation-error"]');
        });
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
    public function countReviews()
    {
        return count($this->getElement('reviews')->findAll('css', '.comment'));
    }

    /**
     * {@inheritdoc}
     */
    public function hasReviewTitled($title)
    {
        return null !== $this->getElement('reviews')->find('css', sprintf('.comment:contains("%s")', $title));
    }

    /**
     * {@inheritdoc}
     */
    public function getAverageRating()
    {
        return (float) $this->getElement('average_rating')->getAttribute('data-average-rating');
    }

    /**
     * {@inheritdoc}
     */
    public function selectOption($optionName, $optionValue)
    {
        $optionElement = $this->getElement('option_select', ['%option-name%' => strtoupper($optionName)]);
        $optionElement->selectOption($optionValue);
    }

    /**
     * {@inheritdoc}
     */
    public function selectVariant($variantName)
    {
        $variantRadio = $this->getElement('variant_radio', ['%variant-name%' => $variantName]);

        $driver = $this->getDriver();
        if ($driver instanceof Selenium2Driver) {
            $variantRadio->click();

            return;
        }

        $this->getDocument()->fillField($variantRadio->getAttribute('name'), $variantRadio->getAttribute('value'));
    }

    /**
     * {@inheritdoc}
     */
    public function isOutOfStock()
    {
        return $this->hasElement('out_of_stock');
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
    public function isMainImageDisplayed()
    {
        $imageElement = $this->getElement('main_image');

        if (null === $imageElement) {
            return false;
        }

        $imageUrl = $imageElement->getAttribute('src');
        $this->getDriver()->visit($imageUrl);
        $pageText = $this->getDocument()->getText();
        $this->getDriver()->back();

        return false === stripos($pageText, '404 Not Found');
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation($productAssociationName)
    {
        return $this->hasElement('association', ['%association-name%' => $productAssociationName]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductInAssociation($productName, $productAssociationName)
    {
        $products = $this->getElement('association', ['%association-name%' => $productAssociationName]);

        Assert::notNull($products);

        return null !== $products->find('css', sprintf('.sylius-product-name:contains("%s")', $productName));
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'association' => '#sylius-product-association-%association-name%',
            'attributes' => '#sylius-product-attributes',
            'average_rating' => '#average-rating',
            'main_image' => '#main-image',
            'name' => '#sylius-product-name',
            'option_select' => '#sylius_add_to_cart_cartItem_variant_%option-name%',
            'out_of_stock' => '#sylius-product-out-of-stock',
            'product_price' => '#product-price',
            'reviews' => '[data-tab="reviews"] .comments',
            'selecting_variants' => "#sylius-product-selecting-variant",
            'validation_errors' => '.sylius-validation-error',
            'variant_radio' => '#sylius-product-variants tbody tr:contains("%variant-name%") input',
        ]);
    }
}
