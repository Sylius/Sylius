<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Product;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Webmozart\Assert\Assert;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_show';
    }

    public function addToCart(): void
    {
        $this->getElement('add_to_cart_button')->click();

        if ($this->getDriver() instanceof Selenium2Driver) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        }
    }

    public function addToCartWithQuantity(string $quantity): void
    {
        $this->getElement('quantity')->setValue($quantity);
        $this->getElement('add_to_cart_button')->click();

        if ($this->getDriver() instanceof Selenium2Driver) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        }
    }

    public function addToCartWithVariant(string $variant): void
    {
        $this->selectVariant($variant);

        $this->getElement('add_to_cart_button')->click();

        if ($this->getDriver() instanceof Selenium2Driver) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        }
    }

    public function addToCartWithOption(ProductOptionInterface $option, string $optionValue): void
    {
        $select = $this->getElement('option_select', ['%optionCode%' => $option->getCode()]);

        $this->getDocument()->selectFieldOption($select->getAttribute('name'), $optionValue);
        $this->getElement('add_to_cart_button')->click();
    }

    public function getAttributeByName(string $name): ?string
    {
        $attributesTable = $this->getElement('attributes');

        $driver = $this->getDriver();
        if ($driver instanceof Selenium2Driver) {
            try {
                $attributesTab = $this->getElement('tab', ['%name%' => 'attributes']);
                if (!$attributesTab->hasAttribute('[data-test-active]')) {
                    $attributesTab->click();
                }
            } catch (ElementNotFoundException $exception) {
                return null;
            }
        }

        $nameTd = $attributesTable->find('css', sprintf('[data-test-product-attribute-name="%s"]', $name));

        if (null === $nameTd) {
            return null;
        }

        $row = $nameTd->getParent();

        return trim($row->find('css', '[data-test-product-attribute-value]')->getText());
    }

    public function getAttributes(): array
    {
        $attributesTable = $this->getElement('attributes');

        return $attributesTable->findAll('css', '[data-test-product-attribute-name]');
    }

    public function getAverageRating(): float
    {
        return (float) $this->getElement('average_rating')->getAttribute('data-test-average-rating');
    }

    public function getCurrentUrl(): string
    {
        return $this->getDriver()->getCurrentUrl();
    }

    public function getCurrentVariantName(): string
    {
        $currentVariantRow = $this->getElement('current_variant_input')->getParent()->getParent();

        return $currentVariantRow->find('css', 'td:first-child')->getText();
    }

    public function getName(): string
    {
        return $this->getElement('product_name')->getText();
    }

    public function getPrice(): string
    {
        return $this->getElement('product_price')->getText();
    }

    public function hasAddToCartButton(): bool
    {
        if (!$this->hasElement('add_to_cart_button')) {
            return false;
        }

        return $this->getElement('add_to_cart_button') !== null && false === $this->getElement('add_to_cart_button')->hasAttribute('disabled');
    }

    public function hasAssociation(string $productAssociationName): bool
    {
        try {
            $this->getElement('association', ['%associationName%' => $productAssociationName]);
        } catch (ElementNotFoundException $e) {
            return false;
        }

        return true;
    }

    public function hasProductInAssociation(string $productName, string $productAssociationName): bool
    {
        $products = $this->getElement('association', ['%associationName%' => $productAssociationName]);

        Assert::notNull($products);

        return $productName === $products->find('css', sprintf('[data-test-product-name="%s"]', $productName))->getText();
    }

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool
    {
        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        if (!$this->hasElement('validation_errors')) {
            return false;
        }

        return $this->getElement('validation_errors')->getText() === $message;
    }

    public function hasReviewTitled(string $title): bool
    {
        try {
            $element = $this->getElement('reviews_comment', ['%title%' => $title]);
        } catch (ElementNotFoundException $e) {
            return false;
        }

        return $title === $element->getAttribute('data-test-comment');
    }

    public function isOutOfStock(): bool
    {
        return $this->hasElement('out_of_stock');
    }

    public function isMainImageDisplayed(): bool
    {
        if (!$this->hasElement('main_image')) {
            return false;
        }

        $imageUrl = $this->getElement('main_image')->getAttribute('src');
        $this->getDriver()->visit($imageUrl);
        $pageText = $this->getDocument()->getText();
        $this->getDriver()->back();

        return false === stripos($pageText, '404 Not Found');
    }

    public function countReviews(): int
    {
        return count($this->getElement('reviews')->findAll('css', '[data-test-comment]'));
    }

    public function selectOption(string $optionCode, string $optionValue): void
    {
        $optionElement = $this->getElement('option_select', ['%optionCode%' => strtoupper($optionCode)]);
        $optionElement->selectOption($optionValue);
    }

    public function selectVariant(string $variantName): void
    {
        $variantRadio = $this->getElement('variant_radio', ['%variantName%' => $variantName]);

        $driver = $this->getDriver();
        if ($driver instanceof Selenium2Driver) {
            $variantRadio->click();

            return;
        }

        $this->getDocument()->fillField($variantRadio->getAttribute('name'), $variantRadio->getAttribute('value'));
    }

    public function visit($url): void
    {
        $absoluteUrl = $this->makePathAbsolute($url);
        $this->getDriver()->visit($absoluteUrl);
    }

    public function open(array $urlParameters = []): void
    {
        $start = microtime(true);
        $end = $start + 5;
        do {
            try {
                parent::open($urlParameters);
                $isOpen = true;
            } catch (UnexpectedPageException $exception) {
                $isOpen = false;
                sleep(1);
            }
        } while (!$isOpen && microtime(true) < $end);

        if (!$isOpen) {
            throw new UnexpectedPageException();
        }
    }

    public function getVariantsNames(): array
    {
        $variantsNames = [];
        /** @var NodeElement $variantRow */
        foreach ($this->getElement('variants_rows')->findAll('css', 'td:first-child') as $variantRow) {
            $variantsNames[] = $variantRow->getText();
        }

        return $variantsNames;
    }

    public function getOptionValues(string $optionCode): array
    {
        $optionElement = $this->getElement('option_select', ['%optionCode%' => strtoupper($optionCode)]);

        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $optionElement->findAll('css', 'option')
        );
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_to_cart_button' => '[data-test-add-to-cart-button]',
            'association' => '[data-test-product-association="%associationName%"]',
            'attributes' => '[data-test-product-attributes]',
            'average_rating' => '[data-test-average-rating]',
            'current_variant_input' => '[data-test-product-variants] td input:checked',
            'main_image' => '[data-test-main-image]',
            'name' => '[data-test-product-name]',
            'option_select' => '#sylius_add_to_cart_cartItem_variant_%optionCode%',
            'out_of_stock' => '[data-test-product-out-of-stock]',
            'product_price' => '[data-test-product-price]',
            'product_name' => '[data-test-product-name]',
            'reviews' => '[data-test-product-reviews]',
            'reviews_comment' => '[data-test-comment="%title%"]',
            'selecting_variants' => '[data-test-product-selecting-variant]',
            'tab' => '[data-test-tab="%name%"]',
            'quantity' => '[data-test-quantity]',
            'validation_errors' => '[data-test-cart-validation-error]',
            'variant_radio' => '[data-test-product-variants] tbody tr:contains("%variantName%") input',
            'variants_rows' => '[data-test-product-variants-row]',
        ]);
    }
}
