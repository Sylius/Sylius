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
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\SlugGenerationHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
class CreateSimpleProductPage extends BaseCreatePage implements CreateSimpleProductPageInterface
{
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return parent::getRouteName() . '_simple';
    }

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
    public function specifySlugIn($slug, $locale)
    {
        $this->activateLanguageTab($locale);

        $this->getElement('slug', ['%locale%' => $locale])->setValue($slug);
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

    /**
     * {@inheritdoc}
     */
    public function addAttribute($attributeName, $value, $localeCode)
    {
        $this->clickTabIfItsNotActive('attributes');
        $this->clickLocaleTabIfItsNotActive($localeCode);

        $attributeOption = $this->getElement('attributes_choice')->find('css', sprintf('option:contains("%s")', $attributeName));
        $this->selectElementFromAttributesDropdown($attributeOption->getAttribute('value'));

        $this->getDocument()->pressButton('Add attributes');
        $this->waitForFormElement();

        $this->getElement('attribute_value', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode])->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValidationErrors($attributeName, $localeCode)
    {
        $this->clickTabIfItsNotActive('attributes');
        $this->clickLocaleTabIfItsNotActive($localeCode);

        $validationError = $this->getElement('attribute')->find('css', '.sylius-validation-error');

        return $validationError->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute($attributeName, $localeCode)
    {
        $this->clickTabIfItsNotActive('attributes');

        $this->getElement('attribute_delete_button', ['%attributeName%' => $attributeName, '%localeCode%' => $localeCode])->press();
    }

    public function checkAttributeErrors($attributeName, $localeCode)
    {
        $this->clickTabIfItsNotActive('attributes');
        $this->clickLocaleTabIfItsNotActive($localeCode);
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
    public function removeAssociatedProduct($productName, ProductAssociationTypeInterface $productAssociationType)
    {
        $this->clickTabIfItsNotActive('associations');

        $item = $this->getElement('association_dropdown_item_selected', [
            '%association%' => $productAssociationType->getName(),
            '%item%' => $productName,
        ]);
        $item->find('css', 'i.delete')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function choosePricingCalculator($name)
    {
        $this->getElement('price_calculator')->selectOption($name);
    }

    /**
     * {@inheritdoc}
     */
    public function checkChannel($channelName)
    {
        $this->getElement('channel_checkbox', ['%channelName%' => $channelName])->check();
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPriceForChannelAndCurrency($price, ChannelInterface $channel, CurrencyInterface $currency)
    {
        $calculatorElement = $this->getElement('calculator');
        $calculatorElement
            ->waitFor(5, function () use ($channel, $currency) {
                return $this->getElement('calculator')->hasField(sprintf('%s %s', $channel->getName(), $currency->getCode()));
            })
        ;

        $calculatorElement->fillField(sprintf('%s %s', $channel->getName(), $currency->getCode()), $price);
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

    /**
     * {@inheritdoc}
     */
    public function selectShippingCategory($shippingCategoryName)
    {
        $this->getElement('shipping_category')->selectOption($shippingCategoryName);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingRequired($isShippingRequired)
    {
        if ($isShippingRequired) {
            $this->getElement('shipping_required')->check();

            return;
        }

        $this->getElement('shipping_required')->uncheck();
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
            'attribute' => '.attribute',
            'attribute_delete_button' => '.tab[data-tab="%localeCode%"] .attribute .label:contains("%attributeName%") ~ button',
            'attribute_value' => '.tab[data-tab="%localeCode%"] .attribute .label:contains("%attributeName%") ~ input',
            'attributes_choice' => '#sylius_product_attribute_choice',
            'calculator' => '#sylius_calculator_container',
            'channel_checkbox' => '.checkbox:contains("%channelName%") input',
            'channel_pricings' => '#sylius_product_variant_channelPricings',
            'code' => '#sylius_product_code',
            'form' => 'form[name="sylius_product"]',
            'images' => '#sylius_product_images',
            'language_tab' => '[data-locale="%locale%"] .title',
            'locale_tab' => '#attributesContainer .menu [data-tab="%localeCode%"]',
            'name' => '#sylius_product_translations_%locale%_name',
            'price' => '#sylius_product_variant_channelPricings > .field:contains("%channelName%") input[name$="[price]"]',
            'original_price' => '#sylius_product_variant_channelPricings > .field:contains("%channelName%") input[name$="[originalPrice]"]',
            'price_calculator' => '#sylius_product_variant_pricingCalculator',
            'shipping_category' => '#sylius_product_variant_shippingCategory',
            'shipping_required' => '#sylius_product_variant_shippingRequired',
            'slug' => '#sylius_product_translations_%locale%_slug',
            'tab' => '.menu [data-tab="%name%"]',
            'toggle_slug_modification_button' => '.toggle-product-slug-modification',
        ]);
    }

    /**
     * @param int $id
     */
    private function selectElementFromAttributesDropdown($id)
    {
        /** @var Selenium2Driver $driver */
        $driver = $this->getDriver();
        Assert::isInstanceOf($driver, Selenium2Driver::class);

        $driver->executeScript('$(\'#sylius_product_attribute_choice\').dropdown(\'show\');');
        $driver->executeScript(sprintf('$(\'#sylius_product_attribute_choice\').dropdown(\'set selected\', \'%s\');', $id));
    }

    /**
     * @param int $timeout
     */
    private function waitForFormElement($timeout = 5)
    {
        $form = $this->getElement('form');
        $this->getDocument()->waitFor($timeout, function () use ($form) {
            return false === strpos($form->getAttribute('class'), 'loading');
        });
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
     * @return NodeElement
     */
    private function getLastImageElement()
    {
        $images = $this->getElement('images');
        $items = $images->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
