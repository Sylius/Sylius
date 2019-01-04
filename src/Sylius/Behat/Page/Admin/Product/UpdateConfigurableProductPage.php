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

namespace Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

class UpdateConfigurableProductPage extends BaseUpdatePage implements UpdateConfigurableProductPageInterface
{
    use ChecksCodeImmutability;

    /** @var array */
    private $imageUrls = [];

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_translations_%s_name', $localeCode), $name
        );
    }

    public function isProductOptionChosen(string $option): bool
    {
        return $this->getElement('options')->find('named', ['option', $option])->hasAttribute('selected');
    }

    public function isProductOptionsDisabled(): bool
    {
        return 'disabled' === $this->getElement('options')->getAttribute('disabled');
    }

    public function isMainTaxonChosen(string $taxonName): bool
    {
        $this->openTaxonBookmarks();
        Assert::notNull($this->getDocument()->find('css', '.search > .text'));

        return $taxonName === $this->getDocument()->find('css', '.search > .text')->getText();
    }

    public function selectMainTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        Assert::isInstanceOf($this->getDriver(), Selenium2Driver::class);

        $this->getDriver()->executeScript(sprintf('$(\'input.search\').val(\'%s\')', $taxon->getName()));
        $this->getElement('search')->click();
        $this->getElement('search')->waitFor(10, function () {
            return $this->hasElement('search_item_selected');
        });
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
        $pageText = $this->getDocument()->getText();
        $this->getDriver()->back();

        return false === stripos($pageText, '404 Not Found');
    }

    public function attachImage(string $path, string $type = null): void
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
                $imageSourceElement->getAttribute('src')
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

    public function countImages(): int
    {
        $imageElements = $this->getImageElements();

        return count($imageElements);
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
