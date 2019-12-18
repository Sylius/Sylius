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
        $this->getDocument()->pressButton('Add to cart');

        if ($this->getDriver() instanceof Selenium2Driver) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        }
    }

    public function addToCartWithQuantity(string $quantity): void
    {
        $this->getDocument()->fillField('Quantity', $quantity);
        $this->getDocument()->pressButton('Add to cart');

        if ($this->getDriver() instanceof Selenium2Driver) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        }
    }

    public function addToCartWithVariant(string $variant): void
    {
        $this->selectVariant($variant);

        $this->getDocument()->pressButton('Add to cart');

        if ($this->getDriver() instanceof Selenium2Driver) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        }
    }

    public function addToCartWithOption(ProductOptionInterface $option, string $optionValue): void
    {
        $select = $this->getDocument()->find('css', sprintf('select#sylius_add_to_cart_cartItem_variant_%s', $option->getCode()));

        $this->getDocument()->selectFieldOption($select->getAttribute('name'), $optionValue);
        $this->getDocument()->pressButton('Add to cart');
    }

    public function getAttributeByName(string $name): ?string
    {
        $attributesTable = $this->getElement('attributes');

        $driver = $this->getDriver();
        if ($driver instanceof Selenium2Driver) {
            try {
                $attributesTab = $this->getElement('tab', ['%name%' => 'attributes']);
                if (!$attributesTab->hasClass('active')) {
                    $attributesTab->click();
                }
            } catch (ElementNotFoundException $exception) {
                return null;
            }
        }

        $nameTdSelector = sprintf('tr > td.sylius-product-attribute-name:contains("%s")', $name);
        $nameTd = $attributesTable->find('css', $nameTdSelector);

        if (null === $nameTd) {
            return null;
        }

        $row = $nameTd->getParent();

        return trim($row->find('css', 'td.sylius-product-attribute-value')->getText());
    }

    public function getAttributes(): array
    {
        $attributesTable = $this->getElement('attributes');

        return $attributesTable->findAll('css', 'tr > td.sylius-product-attribute-name');
    }

    public function getAverageRating(): float
    {
        return (float) $this->getElement('average_rating')->getAttribute('data-average-rating');
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
        return $this->getElement('name')->getText();
    }

    public function getPrice(): string
    {
        return $this->getElement('product_price')->getText();
    }

    public function hasAddToCartButton(): bool
    {
        return $this->getDocument()->hasButton('Add to cart')
            && false === $this->getDocument()->findButton('Add to cart')->hasAttribute('disabled');
    }

    public function hasAssociation(string $productAssociationName): bool
    {
        return $this->hasElement('association', ['%association-name%' => $productAssociationName]);
    }

    public function hasProductInAssociation(string $productName, string $productAssociationName): bool
    {
        $products = $this->getElement('association', ['%association-name%' => $productAssociationName]);

        Assert::notNull($products);

        return null !== $products->find('css', sprintf('.sylius-product-name:contains("%s")', $productName));
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
        return null !== $this->getElement('reviews')->find('css', sprintf('.comment:contains("%s")', $title));
    }

    public function isOutOfStock(): bool
    {
        return $this->hasElement('out_of_stock');
    }

    public function isMainImageDisplayed(): bool
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

    public function countReviews(): int
    {
        return count($this->getElement('reviews')->findAll('css', '.comment'));
    }

    public function selectOption(string $optionName, string $optionValue): void
    {
        $optionElement = $this->getElement('option_select', ['%option-name%' => strtoupper($optionName)]);
        $optionElement->selectOption($optionValue);
    }

    public function selectVariant(string $variantName): void
    {
        $variantRadio = $this->getElement('variant_radio', ['%variant-name%' => $variantName]);

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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'association' => '#sylius-product-association-%association-name%',
            'attributes' => '#sylius-product-attributes',
            'average_rating' => '#average-rating',
            'current_variant_input' => '#sylius-product-variants td input:checked',
            'main_image' => '#main-image',
            'name' => '#sylius-product-name',
            'option_select' => '#sylius_add_to_cart_cartItem_variant_%option-name%',
            'out_of_stock' => '#sylius-product-out-of-stock',
            'product_price' => '#product-price',
            'reviews' => '[data-tab="reviews"] .comments',
            'selecting_variants' => '#sylius-product-selecting-variant',
            'tab' => '.menu [data-tab="%name%"]',
            'validation_errors' => '.sylius-validation-error',
            'variant_radio' => '#sylius-product-variants tbody tr:contains("%variant-name%") input',
        ]);
    }
}
