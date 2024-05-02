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
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\SlugGenerationHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

class CreateSimpleProductPage extends BaseCreatePage implements CreateSimpleProductPageInterface
{
    use SpecifiesItsField;

    public function getRouteName(): string
    {
        return parent::getRouteName() . '_simple';
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->clickTabIfItsNotActive('details');
        $this->activateLanguageTab($localeCode);
        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);

        if (DriverHelper::isJavascript($this->getDriver())) {
            SlugGenerationHelper::waitForSlugGeneration(
                $this->getSession(),
                $this->getElement('slug', ['%locale%' => $localeCode]),
            );
        }
    }

    public function specifySlugIn(?string $slug, string $locale): void
    {
        $this->activateLanguageTab($locale);

        $this->getElement('slug', ['%locale%' => $locale])->setValue($slug);
    }

    public function specifyPrice(ChannelInterface $channel, string $price): void
    {
        $this->getElement('price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void
    {
        $this->getElement('original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function addAttribute(string $attributeName, string $value, string $localeCode): void
    {
        $this->clickTabIfItsNotActive('attributes');

        $attributeOption = $this->getElement('attributes_choice')->find('css', sprintf('option:contains("%s")', $attributeName));
        $this->selectElementFromAttributesDropdown($attributeOption->getAttribute('value'));

        $this->getDocument()->pressButton('Add attributes');
        $this->waitForFormElement();

        if ('' === $value) {
            return;
        }

        $this->getElement('attribute_value', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode])->setValue($value);
    }

    public function selectAttributeValue(string $attributeName, string $value, string $localeCode): void
    {
        $this->getElement('attribute_value_select', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode])->selectOption($value);
    }

    public function addNonTranslatableAttribute(string $attributeName, string $value): void
    {
        $this->clickTabIfItsNotActive('attributes');

        $attributeOption = $this->getElement('attributes_choice')->find('css', sprintf('option:contains("%s")', $attributeName));
        $this->selectElementFromAttributesDropdown($attributeOption->getAttribute('value'));

        $this->getDocument()->pressButton('Add attributes');
        $this->waitForFormElement();

        $this->getElement('non_translatable_attribute_value', ['%attributeName%' => $attributeName])->setValue($value);
    }

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string
    {
        $this->clickTabIfItsNotActive('attributes');

        $validationError = $this->getElement('attribute')->find('css', '.sylius-validation-error');

        return $validationError->getText();
    }

    public function removeAttribute(string $attributeName, string $localeCode): void
    {
        $this->clickTabIfItsNotActive('attributes');

        $this->getElement('attribute_delete_button', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode])->press();
    }

    public function checkAttributeErrors($attributeName, $localeCode): void
    {
        $this->clickTabIfItsNotActive('attributes');
        $this->clickLocaleTabIfItsNotActive($localeCode);
    }

    public function selectMainTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $mainTaxonElement, $taxon->getName());
    }

    public function hasMainTaxonWithName(string $taxonName): bool
    {
        $this->openTaxonBookmarks();
        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        return $taxonName === $mainTaxonElement->find('css', '.search > .text')->getText();
    }

    public function checkProductTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $productTaxonsCodes = [];
        $productTaxonsElement = $this->getElement('product_taxons');
        if ($productTaxonsElement->getValue() !== '') {
            $productTaxonsCodes = explode(',', $productTaxonsElement->getValue());
        }
        $productTaxonsCodes[] = $taxon->getCode();

        $productTaxonsElement->setValue(implode(',', $productTaxonsCodes));
    }

    public function attachImage(string $path, ?string $type = null): void
    {
        $this->clickTabIfItsNotActive('media');

        $filesPath = $this->getParameter('files_path');

        $this->getDocument()->clickLink('Add');

        $imageForm = $this->getLastImageElement();
        if (null !== $type) {
            $imageForm->fillField('Type', $type);
        }

        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath . $path);
    }

    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void
    {
        $this->clickTab('associations');

        $dropdown = $this->getElement('association_dropdown', [
            '%association%' => $productAssociationType->getName(),
        ]);
        $dropdown->click();

        foreach ($productsNames as $productName) {
            $dropdown->waitFor(5, fn () => $this->hasElement('association_dropdown_item', [
                '%association%' => $productAssociationType->getName(),
                '%item%' => $productName,
            ]));

            $item = $this->getElement('association_dropdown_item', [
                '%association%' => $productAssociationType->getName(),
                '%item%' => $productName,
            ]);
            $item->click();
        }
    }

    public function removeAssociatedProduct(string $productName, ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->clickTabIfItsNotActive('associations');

        $item = $this->getElement('association_dropdown_item_selected', [
            '%association%' => $productAssociationType->getName(),
            '%item%' => $productName,
        ]);
        $item->find('css', 'i.delete')->click();
    }

    public function choosePricingCalculator(string $name): void
    {
        $this->getElement('price_calculator')->selectOption($name);
    }

    public function checkChannel(string $channelName): void
    {
        $this->getElement('channel_checkbox', ['%channelName%' => $channelName])->check();
    }

    public function activateLanguageTab(string $locale): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%locale%' => $locale]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }
    }

    public function selectShippingCategory(string $shippingCategoryName): void
    {
        $this->getElement('shipping_category')->selectOption($shippingCategoryName);
    }

    public function setShippingRequired(bool $isShippingRequired): void
    {
        if ($isShippingRequired) {
            $this->getElement('shipping_required')->check();

            return;
        }

        $this->getElement('shipping_required')->uncheck();
    }

    public function getChannelPricingValidationMessage(): string
    {
        return $this->getElement('prices_validation_message')->getText();
    }

    public function cancelChanges(): void
    {
        $this->getElement('cancel_button')->click();
    }

    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        if (!isset($parameters['%locale%'])) {
            $parameters['%locale%'] = 'en_US';
        }

        return parent::getElement($name, $parameters);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'association_dropdown' => '.field > label:contains("%association%") ~ .product-select',
            'association_dropdown_item' => '.field > label:contains("%association%") ~ .product-select > div.menu > div.item:contains("%item%")',
            'association_dropdown_item_selected' => '.field > label:contains("%association%") ~ .product-select > a.label:contains("%item%")',
            'attribute' => '.attribute',
            'attribute_delete_button' => '#attributesContainer .attributes-group .attributes-header:contains("%attributeName%") button',
            'attribute_value' => '#attributesContainer [data-test-product-attribute-value-in-locale="%attributeName% %localeCode%"] input',
            'attribute_value_select' => '#attributesContainer [data-test-product-attribute-value-in-locale="%attributeName% %localeCode%"] select',
            'attributes_choice' => '#sylius_product_attribute_choice',
            'cancel_button' => '[data-test-cancel-changes-button]',
            'channel_checkbox' => '.checkbox:contains("%channelName%") input',
            'code' => '#sylius_product_code',
            'form' => 'form[name="sylius_product"]',
            'images' => '#sylius_product_images',
            'language_tab' => '[data-locale="%locale%"] .title',
            'locale_tab' => '#attributesContainer .menu [data-tab="%localeCode%"]',
            'main_taxon' => '#sylius_product_mainTaxon',
            'product_taxons' => '#sylius_product_productTaxons',
            'name' => '#sylius_product_translations_%locale%_name',
            'non_translatable_attribute_value' => '#attributesContainer [data-test-product-attribute-value-in-locale="%attributeName% "] input',
            'original_price' => '#sylius_product_variant_channelPricings_%channelCode%_originalPrice',
            'price' => '#sylius_product_variant_channelPricings_%channelCode%_price',
            'prices_validation_message' => '#sylius_product_variant_channelPricings ~ .sylius-validation-error, #sylius_product_variant_channelPricings .sylius-validation-error',
            'price_calculator' => '#sylius_product_variant_pricingCalculator',
            'shipping_category' => '#sylius_product_variant_shippingCategory',
            'shipping_required' => '#sylius_product_variant_shippingRequired',
            'slug' => '#sylius_product_translations_%locale%_slug',
            'tab' => '.menu [data-tab="%name%"]',
            'taxonomy' => 'a[data-tab="taxonomy"]',
            'toggle_slug_modification_button' => '.toggle-product-slug-modification',
        ]);
    }

    private function openTaxonBookmarks(): void
    {
        $this->getElement('taxonomy')->click();
    }

    private function selectElementFromAttributesDropdown(string $id): void
    {
        $this->getDriver()->executeScript('$(\'#sylius_product_attribute_choice\').dropdown(\'show\');');
        $this->getDriver()->executeScript(sprintf('$(\'#sylius_product_attribute_choice\').dropdown(\'set selected\', \'%s\');', $id));
    }

    private function waitForFormElement(int $timeout = 5): void
    {
        $form = $this->getElement('form');
        $this->getDocument()->waitFor($timeout, fn () => !str_contains($form->getAttribute('class'), 'loading'));
    }

    private function clickTabIfItsNotActive(string $tabName): void
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }

    private function clickTab(string $tabName): void
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        $attributesTab->click();
    }

    private function clickLocaleTabIfItsNotActive(string $localeCode): void
    {
        $localeTab = $this->getElement('locale_tab', ['%localeCode%' => $localeCode]);
        if (!$localeTab->hasClass('active')) {
            $localeTab->click();
        }
    }

    private function getLastImageElement(): NodeElement
    {
        $images = $this->getElement('images');
        $items = $images->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
