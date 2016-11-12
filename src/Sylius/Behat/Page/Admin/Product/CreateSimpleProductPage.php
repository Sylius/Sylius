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
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
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
        $this->getDocument()->fillField(
            sprintf('sylius_product_translations_%s_name', $localeCode), $name
        );

        $this->waitForSlugGenerationIfNecessary();
    }

    /**
     * {@inheritdoc}
     */
    public function specifySlug($slug)
    {
        $this->getDocument()->fillField('Slug', $slug);
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
    public function addAttribute($attribute, $value)
    {
        $this->clickTabIfItsNotActive('attributes');

        $attributeOption = $this->getElement('attributes_choice')->find('css', sprintf('option:contains("%s")', $attribute));
        $this->selectElementFromAttributesDropdown($attributeOption->getAttribute('value'));

        $this->getDocument()->pressButton('Add attributes');
        $this->waitForFormElement();

        $this->getElement('attribute_value', ['%attribute%' => $attribute])->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute($attribute)
    {
        $this->clickTabIfItsNotActive('attributes');

        $this->getElement('attribute_delete_button', ['%attribute%' => $attribute])->press();
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

    public function enableSlugModification()
    {
        $this->getElement('toggle_slug_modification_button')->press();
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
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'association_dropdown' => '.field > label:contains("%association%") ~ .product-select',
            'association_dropdown_item' => '.field > label:contains("%association%") ~ .product-select > div.menu > div.item:contains("%item%")',
            'association_dropdown_item_selected' => '.field > label:contains("%association%") ~ .product-select > a.label:contains("%item%")',
            'attribute_delete_button' => '.attribute .label:contains("%attribute%") ~ button',
            'attribute_value' => '.attribute .label:contains("%attribute%") ~ input',
            'attributes_choice' => 'select[name="sylius_product_attribute_choice"]',
            'code' => '#sylius_product_code',
            'form' => 'form[name="sylius_product"]',
            'images' => '#sylius_product_images',
            'name' => '#sylius_product_translations_en_US_name',
            'price' => '#sylius_product_variant_price',
            'slug' => '#sylius_product_translations_en_US_slug',
            'tab' => '.menu [data-tab="%name%"]',
            'toggle_slug_modification_button' => '#toggle-slug-modification',
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

        $driver->executeScript('$(\'[name="sylius_product_attribute_choice"]\').dropdown(\'show\');');
        $driver->executeScript(sprintf('$(\'[name="sylius_product_attribute_choice"]\').dropdown(\'set selected\', %s);', $id));
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
     * @return NodeElement
     */
    private function getLastImageElement()
    {
        $images = $this->getElement('images');
        $items = $images->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }

    private function waitForSlugGenerationIfNecessary()
    {
        if ($this->getDriver() instanceof Selenium2Driver) {
            $this->getDocument()->waitFor(10, function () {
                return '' !== $this->getElement('slug')->getValue();
            });
        }
    }
}
