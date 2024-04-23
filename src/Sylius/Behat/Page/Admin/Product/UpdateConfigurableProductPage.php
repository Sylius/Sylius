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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

class UpdateConfigurableProductPage extends BaseUpdatePage implements UpdateConfigurableProductPageInterface
{
    use ChecksCodeImmutability;

    private array $imageUrls = [];

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_translations_%s_name', $localeCode),
            $name,
        );
    }

    public function setMetaKeywords(string $keywords, string $localeCode): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_translations_%s_metaKeywords', $localeCode),
            $keywords,
        );
    }

    public function setMetaDescription(string $description, string $localeCode): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_translations_%s_metaDescription', $localeCode),
            $description,
        );
    }

    public function isProductOptionChosen(string $option): bool
    {
        $optionElement = $this->getElement('options')->getParent();

        return AutocompleteHelper::isValueVisible($this->getSession(), $optionElement, $option);
    }

    public function isProductOptionsDisabled(): bool
    {
        return 'disabled' === $this->getElement('options')->getAttribute('disabled');
    }

    public function hasMainTaxonWithName(string $taxonName): bool
    {
        $this->openTaxonBookmarks();
        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        return $taxonName === $mainTaxonElement->find('css', '.search > .text')->getText();
    }

    public function selectMainTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $this->getDriver()->executeScript(sprintf('$(\'input.search\').val(\'%s\')', $taxon->getName()));
        $this->getElement('search')->click();
        $this->getElement('search')->waitFor(10, fn () => $this->hasElement('search_item_selected'));
        $itemSelected = $this->getElement('search_item_selected');
        $itemSelected->click();
    }

    public function checkChannel(string $channelName): void
    {
        $this->getElement('channel_checkbox', ['%channel%' => $channelName])->check();
    }

    public function isImageWithTypeDisplayed(string $type): bool
    {
        $imageElement = $this->getImageElementByType($type);

        $imageUrl = $imageElement ? $imageElement->find('css', 'img')->getAttribute('src') : $this->provideImageUrlForType($type);
        if (null === $imageElement && null === $imageUrl) {
            return false;
        }

        $this->getDriver()->visit($imageUrl);
        $statusCode = $this->getDriver()->getStatusCode();
        $this->getDriver()->back();

        return in_array($statusCode, [200, 304], true);
    }

    public function hasLastImageAVariant(ProductVariantInterface $productVariant): bool
    {
        $this->clickTabIfItsNotActive('media');

        $imageForm = $this->getLastImageElement();

        return $productVariant->getCode() === $imageForm->find('css', 'input[type="hidden"]')->getValue();
    }

    public function attachImage(string $path, ?string $type = null, ?ProductVariantInterface $productVariant = null): void
    {
        $this->clickTabIfItsNotActive('media');
        $this->getDocument()->clickLink('Add');

        $imageForm = $this->getLastImageElement();

        if (null !== $type) {
            $imageForm->fillField('Type', $type);
        }

        if (null !== $productVariant) {
            $imageForm->find('css', 'input[type="hidden"]')->setValue($productVariant->getCode());
        }

        $filesPath = $this->getParameter('files_path');
        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath . $path);
    }

    public function changeImageWithType(string $type, string $path): void
    {
        $filesPath = $this->getParameter('files_path');

        $imageForm = $this->getImageElementByType($type);
        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath . $path);
    }

    public function removeImageWithType(string $type): void
    {
        $this->clickTabIfItsNotActive('media');

        $imageElement = $this->getImageElementByType($type);
        $imageSourceElement = $imageElement->find('css', 'img');
        if (null !== $imageSourceElement) {
            $this->saveImageUrlForType($type, $imageSourceElement->getAttribute('src'));
        }

        $imageElement->clickLink('Delete');
    }

    public function removeFirstImage(): void
    {
        $this->clickTabIfItsNotActive('media');
        $imageElement = $this->getFirstImageElement();
        $imageTypeElement = $imageElement->find('css', 'input[type=text]');
        $imageSourceElement = $imageElement->find('css', 'img');

        if (null !== $imageTypeElement && null !== $imageSourceElement) {
            $this->saveImageUrlForType(
                $imageTypeElement->getValue(),
                $imageSourceElement->getAttribute('src'),
            );
        }

        $imageElement->clickLink('Delete');
    }

    public function modifyFirstImageType(string $type): void
    {
        $this->clickTabIfItsNotActive('media');

        $firstImage = $this->getFirstImageElement();
        $this->setImageType($firstImage, $type);
    }

    public function selectVariantForFirstImage(ProductVariantInterface $productVariant): void
    {
        $this->clickTabIfItsNotActive('media');

        $imageElement = $this->getFirstImageElement();
        $imageElement->find('css', 'input[type="hidden"]')->setValue($productVariant->getCode());
    }

    public function countImages(): int
    {
        $imageElements = $this->getImageElements();

        return count($imageElements);
    }

    public function goToVariantsList(): void
    {
        $this->getDocument()->clickLink('List variants');
    }

    public function goToVariantCreation(): void
    {
        $this->getDocument()->clickLink('Create');
    }

    public function goToVariantGeneration(): void
    {
        $this->getDocument()->clickLink('Generate');
    }

    public function hasInventoryTab(): bool
    {
        return null !== $this->getDocument()->find('css', '.tab > h3:contains("Inventory")');
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'channel_checkbox' => '.checkbox:contains("%channel%") input',
            'channels' => '#sylius_product_channels',
            'code' => '#sylius_product_code',
            'images' => '#sylius_product_images',
            'main_taxon' => '#sylius_product_mainTaxon',
            'name' => '#sylius_product_translations_en_US_name',
            'options' => '#sylius_product_options',
            'price' => '#sylius_product_variant_price',
            'search' => '.ui.fluid.search.selection.dropdown',
            'search_item_selected' => 'div.menu > div.item.selected',
            'tab' => '.menu [data-tab="%name%"]',
            'taxonomy' => 'a[data-tab="taxonomy"]',
        ]);
    }

    private function openTaxonBookmarks(): void
    {
        $this->getElement('taxonomy')->click();
    }

    private function clickTabIfItsNotActive(string $tabName): void
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }

    private function getImageElementByType(string $type): ?NodeElement
    {
        $images = $this->getElement('images');
        $typeInput = $images->find('css', 'input[value="' . $type . '"]');

        if (null === $typeInput) {
            return null;
        }

        return $typeInput->getParent()->getParent()->getParent();
    }

    /**
     * @return NodeElement[]
     */
    private function getImageElements(): array
    {
        $images = $this->getElement('images');

        return $images->findAll('css', 'div[data-form-collection="item"]');
    }

    private function getLastImageElement(): NodeElement
    {
        $imageElements = $this->getImageElements();

        Assert::notEmpty($imageElements);

        return end($imageElements);
    }

    private function getFirstImageElement(): NodeElement
    {
        $imageElements = $this->getImageElements();

        Assert::notEmpty($imageElements);

        return reset($imageElements);
    }

    private function setImageType(NodeElement $imageElement, string $type): void
    {
        $typeField = $imageElement->findField('Type');
        $typeField->setValue($type);
    }

    private function provideImageUrlForType(string $type): ?string
    {
        return $this->imageUrls[$type] ?? null;
    }

    private function saveImageUrlForType(string $type, string $imageUrl): void
    {
        $this->imageUrls[$type] = $imageUrl;
    }
}
