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
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

trait FormTrait
{
    public function getDefinedFormElements(): array
    {
        return [
            'attribute_value' => '[data-test-attribute-value][data-test-locale-code="%localeCode%"][data-test-attribute-name="%attributeName%"]',
            'channel_tab' => '[data-test-channel-tab="%channelCode%"]',
            'form' => '[data-live-name-value="SyliusAdmin.Product.Form"]',
            'field_name' => '[name="sylius_product[translations][%localeCode%][name]"]',
            'field_slug' => '[name="sylius_product[translations][%localeCode%][slug]"]',
            'field_price' => '[name="sylius_product[variant][channelPricings][%channelCode%][price]"]',
            'field_original_price' => '[name="sylius_product[variant][channelPricings][%channelCode%][originalPrice]"]',
            'field_shipping_category' => '[name="sylius_product[variant][shippingCategory]"]',
            'field_shipping_required' => '[name="sylius_product[variant][shippingRequired]"]',
            'generate_product_slug_button' => '[data-test-generate-product-slug-button="%localeCode%"]',
            'images' => '[data-test-images]',
            'image_subform' => '[data-test-image-subform]',
            'image_subform_with_type' => '[data-test-image-subform][data-test-type="%type%"]',
            'images_subforms' => '[data-test-image-subforms]',
            'product_attribute_autocomplete' => '[data-test-product-attribute-autocomplete]',
            'product_attribute_delete_button' => '[data-test-product-attribute-delete-button="%attributeName%"]',
            'product_attribute_input' => 'input[name="product_attributes"]',
            'product_attribute_tab' => '[data-test-product-attribute-tab="%name%"]',
            'product_options_autocomplete' => '[data-test-product-options-autocomplete]',
            'product_translation_accordion' => '[data-test-product-translation-accordion="%localeCode%"]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
        ];
    }

    /*
     * Filling fields
     */

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->changeTab('translations');
        $this->expandTranslationAccordion($localeCode);
        $this->getElement('field_name', ['%localeCode%' => $localeCode])->setValue($name);

        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getElement('generate_product_slug_button', ['%localeCode%' => $localeCode])->click();
            $this->waitForFormUpdate();
        }
    }

    public function specifyPrice(ChannelInterface $channel, string $price): void
    {
        $this->changeTab('channel-pricing');
        $this->changeChannelTab($channel->getCode());
        $this->getElement('field_price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    // TODO: Move to the Simple Product specific class
    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void
    {
        $this->changeTab('channel-pricing');
        $this->changeChannelTab($channel->getCode());
        $this->getElement('field_original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    // TODO: Move to the Simple Product specific class
    public function selectShippingCategory(string $shippingCategoryName): void
    {
        $this->changeTab('shipping');
        $this->getElement('field_shipping_category')->selectOption($shippingCategoryName);
    }

    // TODO: Move to the Simple Product specific class
    public function setShippingRequired(bool $isShippingRequired): void
    {
        $this->changeTab('details');

        if ($isShippingRequired) {
            $this->getElement('field_shipping_required')->check();

            return;
        }

        $this->getElement('field_shipping_required')->uncheck();
    }

    // TODO: Move to the Simple Product specific class
    public function isShippingRequired(): bool
    {
        return $this->getElement('field_shipping_required')->isChecked();
    }

    // TODO: Move to the Configurable Product specific class
    public function selectOption(string $optionName): void
    {
        $this->changeTab('details');
        $productOptionsAutocomplete = $this->getElement('product_options_autocomplete');

        $this->autocompleteHelper->selectByName($this->getDriver(), $productOptionsAutocomplete->getXpath(), $optionName);
    }

    /*
     * Tabs management
     */

    private function changeTab(string $tabName): void
    {
        $this->getElement('side_navigation_tab', ['%name%' => $tabName])->click();
    }

    private function changeChannelTab(string $channelCode): void
    {
        $this->getElement('channel_tab', ['%channelCode%' => $channelCode])->click();
    }

    private function changeAttributeTab(string $attributeName): void
    {
        $this->getElement('product_attribute_tab', ['%name%' => $attributeName])->click();
    }

    private function expandTranslationAccordion(string $localeCode): void
    {
        $translationAccordion = $this->getElement('product_translation_accordion', ['%localeCode%' => $localeCode]);

        if ($translationAccordion->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $translationAccordion->click();
    }

    /*
     * Attribute management
     */

    public function addAttribute(string $attributeName): void
    {
        $this->changeTab('attributes');
        $this->selectAttributeToBeAdded($attributeName);
        $this->clickButton('Add');

        $this->waitForFormUpdate();
    }

    public function updateAttribute(string $attributeName, string $value, string $localeCode): void
    {
        $this->changeTab('attributes');
        $this->changeAttributeTab($attributeName);

        $attributeValue = $this->getElement('attribute_value', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode]);

        match ($attributeValue->getTagName()) {
            'input' => $attributeValue->setValue($value),
            'select' => $attributeValue->selectOption($value),
            default => throw new \InvalidArgumentException('Unsupported attribute value type'),
        };

        $attributeValue->blur();
        $this->waitForFormUpdate();
    }

    public function removeAttribute(string $attributeName, string $localeCode): void
    {
        $this->changeTab('attributes');

        $this->getElement('product_attribute_delete_button', ['%attributeName%' => $attributeName])->press();

        $this->waitForFormUpdate();
    }

    public function getAttributeValue(string $attribute, string $localeCode): string
    {
        $this->changeTab('attributes');
        $this->changeAttributeTab($attribute);

        $attributeValue = $this->getElement('attribute_value', ['%attributeName%' => $attribute, '%localeCode%' => $localeCode]);

        return match ($attributeValue->getTagName()) {
            'input' => $attributeValue->getValue(),
            'select' => $attributeValue->getText(),
            default => throw new \InvalidArgumentException('Unsupported attribute value type'),
        };
    }

    public function addSelectedAttributes(): void
    {
        $this->changeTab('attributes');
        $this->clickButton('Add');
        $this->waitForFormUpdate();
    }

    public function getNumberOfAttributes(): int
    {
        return count($this->getDocument()->findAll('css', '[data-test-product-attribute-tab]'));
    }

    private function selectAttributeToBeAdded(string $attributeName): void
    {
        $driver = $this->getDriver();
        $this->autocompleteHelper->selectByName(
            $driver,
            $this->getElement('product_attribute_input')->getXpath(),
            $attributeName,
        );
    }

    /*
     * Media management
     */

    public function attachImage(string $path, string $type = null, ?ProductVariantInterface $productVariant = null): void
    {
        $this->changeTab('media');
        $this->clickButton('Add image');
        $this->waitForFormUpdate();

        $images = $this->getElement('images');
        $imagesSubform = $images->findAll('css', '[data-test-image-subform]');
        $imageSubform = end($imagesSubform);

        if (null !== $type) {
            $imageSubform->fillField('Type', $type);
        }

        if (null !== $productVariant) {
            $this->autocompleteHelper->selectByValue(
                $this->getDriver(),
                $imageSubform->find('css', '[data-test-product-variant]')->getXpath(),
                $productVariant->getCode(),
            );
        }

        $filesPath = $this->getParameter('files_path');
        $imageSubform->find('css', '[data-test-file]')->attachFile($filesPath . $path);
    }

    public function changeImageWithType(string $type, string $path): void
    {
        $filesPath = $this->getParameter('files_path');

        $imageSubform = $this->getElement('image_subform_with_type', ['%type%' => $type]);
        $imageSubform->find('css', '[data-test-file]')->attachFile($filesPath . $path);
    }

    public function removeImageWithType(string $type): void
    {
        $this->changeTab('media');

        $imageSubform = $this->getElement('image_subform_with_type', ['%type%' => $type]);
        $imageSubform->find('css', '[data-test-image-delete]')->click();
        $this->waitForFormUpdate();
    }

    public function removeFirstImage(): void
    {
        $this->changeTab('media');
        $firstSubform = $this->getFirstImageSubform();
        $firstSubform->findAll('css', '[data-test-image-delete]')[0]->click();
    }

    public function hasImageWithType(string $type): bool
    {
        $this->changeTab('media');
        try {
            $imageSubform = $this->getElement('image_subform_with_type', ['%type%' => $type]);
        } catch (ElementNotFoundException) {
            return false;
        }

        $imageUrl = $imageSubform->getAttribute('data-test-image-url');
        $this->getDriver()->visit($imageUrl);
        $statusCode = $this->getDriver()->getStatusCode();
        $this->getDriver()->back();

        return in_array($statusCode, [200, 304], true);
    }

    public function hasImageWithVariant(ProductVariantInterface $productVariant): bool
    {
        $this->changeTab('media');
        $images = $this->getElement('images');

        return $images->has('css', sprintf('[data-test-product-variant="%s"]', $productVariant->getCode()));
    }

    public function countImages(): int
    {
        $images = $this->getElement('images');
        $imageSubforms = $images->findAll('css', '[data-test-image-subform]');

        return count($imageSubforms);
    }

    public function modifyFirstImageType(string $type): void
    {
        $this->changeTab('media');

        $firstImageSubform = $this->getFirstImageSubform();

        $firstImageSubform->find('css', 'input[data-test-type]')->setValue($type);
    }

    public function selectVariantForFirstImage(ProductVariantInterface $productVariant): void
    {
        $this->changeTab('media');

        $imageSubform = $this->getFirstImageSubform();
        $this->autocompleteHelper->selectByValue(
            $this->getDriver(),
            $imageSubform->find('css', '[data-test-product-variant]')->getXpath(),
            $productVariant->getCode(),
        );
    }

    private function getFirstImageSubform(): NodeElement
    {
        $images = $this->getElement('images');
        $imageSubforms = $images->findAll('css', '[data-test-image-subform]');

        return reset($imageSubforms);
    }

    /*
     * Helpers
     */

    private function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
    }

    private function clickButton(string $locator): void
    {
        $this->getDocument()->pressButton($locator);
    }
}
