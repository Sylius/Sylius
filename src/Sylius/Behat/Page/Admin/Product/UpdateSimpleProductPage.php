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
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdateSimpleProductPage extends BaseUpdatePage implements UpdateSimpleProductPageInterface
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

        $this->waitForSlugGenerationIfNecessary();
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPrice($price)
    {
        $this->getDocument()->fillField('Price', $price);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValue($attribute)
    {
        $attributesTab = $this->getElement('tab', ['%name%' => 'attributes']);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }

        return $this->getElement('attribute', ['%attribute%' => $attribute])->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute($attribute)
    {
        return null !== $this->getDocument()->find('css', sprintf('.attribute .label:contains("%s")', $attribute));
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
        $this->getElement('search')->waitFor(10,
            function () {
                return $this->hasElement('search_item_selected');
            });
        $itemSelected = $this->getElement('search_item_selected');
        $itemSelected->click();
    }

    /**
     * {@inheritdoc}
     */
    public function isMainTaxonChosen($taxonName)
    {
        $this->openTaxonBookmarks();

        return $taxonName === $this->getDocument()->find('css', '.search > .text')->getText();
    }

    public function disableTracking()
    {
        $this->getElement('tracked')->uncheck();
    }

    public function enableTracking()
    {
        $this->getElement('tracked')->check();
    }

    /**
     * {@inheritdoc}
     */
    public function isTracked()
    {
        return $this->getElement('tracked')->isChecked();
    }

    /**
     * {@inheritdoc}
     */
    public function isImageWithCodeDisplayed($code)
    {
        $imageElement = $this->getImageElementByCode($code);

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
    public function attachImage($path, $code = null)
    {
        $this->clickTabIfItsNotActive('media');

        $filesPath = $this->getParameter('files_path');

        $this->getDocument()->clickLink('Add');

        $imageForm = $this->getLastImageElement();
        if (null !== $code) {
            $imageForm->fillField('Code', $code);
        }

        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function changeImageWithCode($code, $path)
    {
        $filesPath = $this->getParameter('files_path');

        $imageForm = $this->getImageElementByCode($code);
        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function removeImageWithCode($code)
    {
        $this->clickTabIfItsNotActive('media');

        $imageElement = $this->getImageElementByCode($code);
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
    public function countImages()
    {
        $imageElements = $this->getImageElements();

        return count($imageElements);
    }

    /**
     * {@inheritdoc}
     */
    public function isImageCodeDisabled()
    {
        return 'disabled' === $this->getLastImageElement()->findField('Code')->getAttribute('disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function isSlugReadOnly()
    {
        return 'readonly' === $this->getElement('slug')->getAttribute('readonly');
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationMessageForImage()
    {
        $this->clickTabIfItsNotActive('media');

        $imageForm = $this->getLastImageElement();

        $foundElement = $imageForm->find('css', '.sylius-validation-error');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames)
    {
        $this->clickTab('associations');

        Assert::isInstanceOf($this->getDriver(), Selenium2Driver::class);

        $dropdown = $this->getElement('association_dropdown', [
            '%association%' => $productAssociationType->getName()
        ]);
        $dropdown->click();
        $dropdown->waitFor(10, function () use ($productsNames, $productAssociationType) {
            return $this->hasElement('association_dropdown_item', [
                '%association%' => $productAssociationType->getName(),
                '%item%' => $productsNames[0],
            ]);
        });

        foreach ($productsNames as $productName) {
            $item = $this->getElement('association_dropdown_item', [
                '%association%' => $productAssociationType->getName(),
                '%item%' => $productName,
            ]);
            $item->click();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociatedProduct($productName, ProductAssociationTypeInterface $productAssociationType)
    {
        $this->clickTabIfItsNotActive('associations');

        return $this->hasElement('association_dropdown_item', [
            '%association%' => $productAssociationType->getName(),
            '%item%' => $productName,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssociatedProduct($productName, ProductAssociationTypeInterface $productAssociationType)
    {
        $this->clickTabIfItsNotActive('associations');

        $item = $this->getElement('association_dropdown_item_selected', [
            '%association%' => $productAssociationType->getName(),
            '%item%' => $productName,
        ]);

        $deleteIcon = $item->find('css', 'i.delete');
        Assert::notNull($deleteIcon);
        $deleteIcon->click();
    }

    /**
     * {@inheritdoc}
     */
    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency)
    {
        $priceConfigurationElement = $this->getElement('pricing_configuration');
        $priceElement = $priceConfigurationElement
            ->find('css', sprintf('label:contains("%s %s")', $channel->getCode(), $currency->getCode()))->getParent();

        return $priceElement->find('css', 'input')->getValue();
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
            'association_dropdown' => '.field > label:contains("%association%") ~ .product-select',
            'association_dropdown_item' => '.field > label:contains("%association%") ~ .product-select > div.menu > div.item:contains("%item%")',
            'association_dropdown_item_selected' => '.field > label:contains("%association%") ~ .product-select > a.label:contains("%item%")',
            'attribute' => '.attribute .label:contains("%attribute%") ~ input',
            'code' => '#sylius_product_code',
            'images' => '#sylius_product_images',
            'name' => '#sylius_product_translations_en_US_name',
            'price' => '#sylius_product_variant_price',
            'pricing_configuration' => '#sylius_calculator_container',
            'search' => '.ui.fluid.search.selection.dropdown',
            'search_item_selected' => 'div.menu > div.item.selected',
            'slug' => '#sylius_product_translations_en_US_slug',
            'tab' => '.menu [data-tab="%name%"]',
            'taxonomy' => 'a[data-tab="taxonomy"]',
            'tracked' => '#sylius_product_variant_tracked',
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
     * @param string $tabName
     */
    private function clickTab($tabName)
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        $attributesTab->click();
    }

    /**
     * @param string $code
     *
     * @return NodeElement
     */
    private function getImageElementByCode($code)
    {
        $images = $this->getElement('images');
        $inputCode = $images->find('css', 'input[value="'.$code.'"]');

        if (null === $inputCode) {
            return null;
        }

        return $inputCode->getParent()->getParent()->getParent();
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

    private function waitForSlugGenerationIfNecessary()
    {
        if (!$this->getDriver() instanceof Selenium2Driver) {
            return;
        }

        $slugElement = $this->getElement('slug');
        if ($slugElement->hasAttribute('readonly')) {
            return;
        }

        $value = $slugElement->getValue();
        $this->getDocument()->waitFor(10, function () use ($slugElement, $value) {
            return $value !== $slugElement->getValue();
        });
    }
}
