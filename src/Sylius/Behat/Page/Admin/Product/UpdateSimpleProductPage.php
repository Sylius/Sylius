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
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\SlugGenerationHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
class UpdateSimpleProductPage extends BaseUpdatePage implements UpdateSimpleProductPageInterface
{
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function nameItIn($name, $localeCode)
    {
        $this->activateLanguageTab($localeCode);
        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);

        if ($this->getDriver() instanceof Selenium2Driver) {
            SlugGenerationHelper::waitForSlugGeneration(
                $this->getSession(),
                $this->getElement('slug', ['%locale%' => $localeCode])
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPrice($channelName, $price)
    {
        $this->getElement('price', ['%channelName%' => $channelName])->setValue($price);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyOriginalPrice($channelName, $originalPrice)
    {
        $this->getElement('original_price', ['%channelName%' => $channelName])->setValue($originalPrice);
    }

    public function addSelectedAttributes()
    {
        $this->clickTabIfItsNotActive('attributes');
        $this->getDocument()->pressButton('Add attributes');

        $form = $this->getDocument()->find('css', 'form');

        $this->getDocument()->waitFor(1, function () use ($form) {
            return $form->hasClass('loading');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute($attributeName, $localeCode)
    {
        $this->clickTabIfItsNotActive('attributes');

        $this->getElement('attribute_delete_button', ['%attributeName%' => $attributeName, '$localeCode%' => $localeCode])->press();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValue($attribute, $localeCode)
    {
        $this->clickTabIfItsNotActive('attributes');
        $this->clickLocaleTabIfItsNotActive($localeCode);

        return $this->getElement('attribute', ['%attributeName%' => $attribute, '%localeCode%' => $localeCode])->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValidationErrors($attributeName, $localeCode)
    {
        $this->clickTabIfItsNotActive('attributes');
        $this->clickLocaleTabIfItsNotActive($localeCode);

        $validationError = $this->getElement('attribute_element')->find('css', '.sylius-validation-error');

        return $validationError->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfAttributes()
    {
        return count($this->getDocument()->findAll('css', '.attribute'));
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute($attributeName)
    {
        return null !== $this->getDocument()->find('css', sprintf('.attribute .label:contains("%s")', $attributeName));
    }

    /**
     * {@inheritdoc}
     */
    public function selectMainTaxon(TaxonInterface $taxon)
    {
        $this->openTaxonBookmarks();

        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $mainTaxonElement, $taxon->getName());
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
    public function enableSlugModification($locale)
    {
        SlugGenerationHelper::enableSlugModification(
            $this->getSession(),
            $this->getElement('toggle_slug_modification_button', ['%locale%' => $locale])
        );
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
        $this->clickTabIfItsNotActive('media');

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
    public function isSlugReadonlyIn($locale)
    {
        return SlugGenerationHelper::isSlugReadonly(
            $this->getSession(),
            $this->getElement('slug', ['%locale%' => $locale])
        );
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

        foreach ($productsNames as $productName) {
            $dropdown->waitFor(5, function () use ($productName, $productAssociationType) {
                return $this->hasElement('association_dropdown_item', [
                    '%association%' => $productAssociationType->getName(),
                    '%item%' => $productName,
                ]);
            });

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
    public function getSlug($locale)
    {
        $this->activateLanguageTab($locale);

        return $this->getElement('slug', ['%locale%' => $locale])->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function specifySlugIn($slug, $locale)
    {
        $this->activateLanguageTab($locale);

        $this->getElement('slug', ['%locale%' => $locale])->setValue($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function activateLanguageTab($locale)
    {
        if (!$this->getDriver() instanceof Selenium2Driver) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%locale%' => $locale]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }
    }

    public function getPriceForChannel($channelName)
    {
        return $this->getElement('price', ['%channelName%' => $channelName])->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalPriceForChannel($channelName)
    {
        return $this->getElement('original_price', ['%channelName%' => $channelName])->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingRequired()
    {
        return $this->getElement('shipping_required')->isChecked();
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
    protected function getElement($name, array $parameters = [])
    {
        if (!isset($parameters['%locale%'])) {
            $parameters['%locale%'] = 'en_US';
        }

        return parent::getElement($name, $parameters);
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
            'attribute' => '.tab[data-tab="%localeCode%"] .attribute .label:contains("%attributeName%") ~ input',
            'attribute_element' => '.attribute',
            'attribute_delete_button' => '.tab[data-tab="%localeCode%"] .attribute .label:contains("%attributeName%") ~ button',
            'code' => '#sylius_product_code',
            'images' => '#sylius_product_images',
            'language_tab' => '[data-locale="%locale%"] .title',
            'locale_tab' => '#attributesContainer .menu [data-tab="%localeCode%"]',
            'name' => '#sylius_product_translations_%locale%_name',
            'original_price' => '#sylius_product_variant_channelPricings > .field:contains("%channelName%") input[name$="[originalPrice]"]',
            'price' => '#sylius_product_variant_channelPricings > .field:contains("%channelName%") input[name$="[price]"]',
            'pricing_configuration' => '#sylius_calculator_container',
            'main_taxon' => '#sylius_product_mainTaxon',
            'shipping_required' => '#sylius_product_variant_shippingRequired',
            'slug' => '#sylius_product_translations_%locale%_slug',
            'tab' => '.menu [data-tab="%name%"]',
            'taxonomy' => 'a[data-tab="taxonomy"]',
            'tracked' => '#sylius_product_variant_tracked',
            'toggle_slug_modification_button' => '[data-locale="%locale%"] .toggle-product-slug-modification',
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
     * @param string $localeCode
     */
    private function clickLocaleTabIfItsNotActive($localeCode)
    {
        $localeTab = $this->getElement('locale_tab', ['%localeCode%' => $localeCode]);
        if (!$localeTab->hasClass('active')) {
            $localeTab->click();
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
