<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdateConfigurableProductPage extends BaseUpdatePage implements UpdateConfigurableProductPageInterface
{
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function nameItIn($name, $localeCode)
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_translations_%s_name', $localeCode), $name
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isProductOptionChosen($option)
    {
        return $this->getElement('options')->find('named', array('option', $option))->hasAttribute('selected');
    }

    /**
     * @return bool
     */
    public function isProductOptionsDisabled()
    {
        return 'disabled' === $this->getElement('options')->getAttribute('disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function isMainTaxonChosen($taxonName)
    {
        $this->openTaxonBookmarks();
        Assert::notNull($this->getDocument()->find('css', '.search > .text'));

        return $taxonName === $this->getDocument()->find('css', '.search > .text')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function selectMainTaxon(TaxonInterface $taxon)
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

    /**
     * {@inheritdoc}
     */
    public function checkChannel($channelName)
    {
        $this->getElement('channel_checkbox', ['%channel%' => $channelName])->check();
    }

    /**
     * {@inheritdoc}
     */
    public function isImageWithTypeDisplayed($type)
    {
        $imageElement = $this->getImageElementByType($type);

        if (null === $imageElement) {
            return false;
        }

        $imageUrl = $imageElement->find('css', 'img')->getAttribute('src');
        $this->getDriver()->visit($imageUrl);
        $pageText = $this->getDocument()->getText();
        $this->getDriver()->back();

        return false === stripos($pageText, '404 Not Found');
    }

    /**
     * {@inheritdoc}
     */
    public function attachImage($path, $type = null)
    {
        $this->clickTabIfItsNotActive('media');

        $filesPath = $this->getParameter('files_path');

        $this->getDocument()->clickLink('Add');

        $imageForm = $this->getLastImageElement();
        if (null !== $type) {
            $imageForm->fillField('Type', $type);
        }

        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function changeImageWithType($type, $path)
    {
        $filesPath = $this->getParameter('files_path');

        $imageForm = $this->getImageElementByType($type);
        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function removeImageWithType($type)
    {
        $this->clickTabIfItsNotActive('media');

        $imageElement = $this->getImageElementByType($type);
        $imageElement->clickLink('Delete');
    }

    public function removeFirstImage()
    {
        $imageElement = $this->getFirstImageElement();
        $imageElement->clickLink('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function modifyFirstImageType($type)
    {
        $this->clickTabIfItsNotActive('media');

        $firstImage = $this->getFirstImageElement();
        $this->setImageType($firstImage, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function countImages()
    {
        $imageElements = $this->getImageElements();

        return count($imageElements);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
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

    private function openTaxonBookmarks()
    {
        $this->getElement('taxonomy')->click();
    }

    /**
     * @param string $tabName
     */
    private function clickTabIfItsNotActive($tabName)
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }

    /**
     * @param string $type
     *
     * @return NodeElement
     */
    private function getImageElementByType($type)
    {
        $images = $this->getElement('images');
        $typeInput = $images->find('css', 'input[value="'.$type.'"]');

        if (null === $typeInput) {
            return null;
        }

        return $typeInput->getParent()->getParent()->getParent();
    }

    /**
     * @return NodeElement[]
     */
    private function getImageElements()
    {
        $images = $this->getElement('images');

        return $images->findAll('css', 'div[data-form-collection="item"]');
    }

    /**
     * @return NodeElement
     */
    private function getLastImageElement()
    {
        $imageElements = $this->getImageElements();

        Assert::notEmpty($imageElements);

        return end($imageElements);
    }

    /**
     * @return NodeElement
     */
    private function getFirstImageElement()
    {
        $imageElements = $this->getImageElements();

        Assert::notEmpty($imageElements);

        return reset($imageElements);
    }

    /**
     * @param NodeElement $imageElement
     * @param string $type
     */
    private function setImageType(NodeElement $imageElement, $type)
    {
        $typeField = $imageElement->findField('Type');
        $typeField->setValue($type);
    }
}
