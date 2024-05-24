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

namespace Sylius\Behat\Page\Admin\Product\SimpleProduct;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

trait SimpleProductFormTrait
{
    public function getDefinedFormElements(): array
    {
        return [
            'add-image' => '[data-test-add-image]',
            'attribute_value' => '[data-test-attribute-value][data-test-locale-code="%localeCode%"][data-test-attribute-name="%attributeName%"]',
            'channel' => '[data-test-channel-code="%channel_code%"]',
            'channel_tab' => '[data-test-channel-tab="%channelCode%"]',
            'channels' => '[data-test-channels]',
            'code' => '[data-test-code]',
            'enabled' => '[data-test-enabled]',
            'field_associations' => '[name="sylius_admin_product[associations][%association%][]"]',
            'field_name' => '[data-test-name="%locale%"]',
            'field_original_price' => '[data-test-original-price-in-channel="%channelCode%"]',
            'field_price' => '[data-test-price-in-channel="%channelCode%"]',
            'field_shipping_category' => '[name="sylius_admin_product[variant][shippingCategory]"]',
            'field_shipping_required' => '[name="sylius_admin_product[variant][shippingRequired]"]',
            'form' => '[data-live-name-value="sylius_admin:product:form"]',
            'generate_product_slug_button' => '[data-test-generate-product-slug-button="%localeCode%"]',
            'image_subform_with_type' => '[data-test-image-subform][data-test-type="%type%"]',
            'images' => '[data-test-images]',
            'name' => '[data-test-name="%locale%"]',
            'meta_description' => '[data-test-meta-description="%locale%"]',
            'meta_keywords' => '[data-test-meta-keywords="%locale%"]',
            'prices_validation_message' => '[data-test-missing-channel-price]',
            'product_attribute_delete_button' => '[data-test-product-attribute-delete-button="%attributeName%"]',
            'product_attribute_input' => 'input[name="product_attributes"]',
            'product_attribute_tab' => '[data-test-product-attribute-tab="%name%"]',
            'product_translation_accordion' => '[data-test-product-translations-accordion="%localeCode%"]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            'slug' => '[data-test-slug="%locale%"]',
        ];
    }

    public function specifyCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function specifyField(string $field, string $value): void
    {
        $this->getElement($field)->setValue($value);
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->changeTab('translations');
        $this->expandTranslationAccordion($localeCode);
        $this->getElement('field_name', ['%localeCode%' => $localeCode])->setValue($name);
    }

    public function specifyPrice(ChannelInterface $channel, string $price): void
    {
        $this->changeTab('channel-pricing');
        $this->changeChannelTab($channel->getCode());
        $this->getElement('field_price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void
    {
        $this->changeTab('channel-pricing');
        $this->changeChannelTab($channel->getCode());
        $this->getElement('field_original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function selectShippingCategory(string $shippingCategoryName): void
    {
        $this->changeTab('shipping');
        $this->getElement('field_shipping_category')->selectOption($shippingCategoryName);
    }

    public function setShippingRequired(bool $isShippingRequired): void
    {
        $this->changeTab('details');

        if ($isShippingRequired) {
            $this->getElement('field_shipping_required')->check();

            return;
        }

        $this->getElement('field_shipping_required')->uncheck();
    }

    public function isShippingRequired(): bool
    {
        return $this->getElement('field_shipping_required')->isChecked();
    }

    public function generateSlug(string $localeCode): void
    {
        $this->getElement('generate_product_slug_button', ['%localeCode%' => $localeCode])->click();
        $this->waitForFormUpdate();
    }

    public function hasTab(string $name): bool
    {
        return $this->hasElement('side_navigation_tab', ['%name%' => $name]);
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
     * Associations management
     */

    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void
    {
        $this->changeTab('associations');
        $associationField = $this->getElement('field_associations', ['%association%' => $productAssociationType->getCode()]);

        foreach ($productsNames as $productName) {
            $this->autocompleteHelper->selectByName(
                $this->getDriver(),
                $associationField->getXpath(),
                $productName,
            );
            $this->waitForFormUpdate();
        }
    }

    public function removeAssociatedProduct(ProductInterface $product, ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->changeTab('associations');
        $associationField = $this->getElement('field_associations', ['%association%' => $productAssociationType->getCode()]);

        $this->autocompleteHelper->removeByValue(
            $this->getDriver(),
            $associationField->getXpath(),
            $product->getCode(),
        );
    }

    public function hasAssociatedProduct(ProductInterface $product, ProductAssociationTypeInterface $productAssociationType): bool
    {
        $this->changeTab('associations');
        $associationField = $this->getElement('field_associations', ['%association%' => $productAssociationType->getCode()]);

        return in_array($product->getCode(), $associationField->getValue(), true);
    }

    /*
     * Media management
     */

    public function attachImage(string $path, ?string $type = null, ?ProductVariantInterface $productVariant = null): void
    {
        $this->changeTab('media');
        $this->getElement('add-image')->click();
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

    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        if (!isset($parameters['%locale%'])) {
            $parameters['%locale%'] = 'en_US';
        }

        return parent::getElement($name, $parameters);
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
        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getDocument()->pressButton($locator);
        }
    }

    /*
     * Tabs management
     */
    private function changeTab(string $tabName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => $tabName])->click();
    }

    private function changeChannelTab(string $channelCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('channel_tab', ['%channelCode%' => $channelCode])->click();
    }

    private function changeAttributeTab(string $attributeName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('product_attribute_tab', ['%name%' => $attributeName])->click();
    }

    private function expandTranslationAccordion(string $localeCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $translationAccordion = $this->getElement('product_translation_accordion', ['%localeCode%' => $localeCode]);

        if ($translationAccordion->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $translationAccordion->click();
    }
}
