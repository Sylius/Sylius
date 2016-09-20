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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Templating\Tests\Storage\FileStorageTest;
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
            'code' => '#sylius_product_code',
            'name' => '#sylius_product_translations_en_US_name',
            'options' => '#sylius_product_options',
            'price' => '#sylius_product_variant_price',
            'search' => '.ui.fluid.search.selection.dropdown',
            'search_item_selected' => 'div.menu > div.item.selected',            
            'taxonomy' => 'a[data-tab="taxonomy"]',
        ]);
    }

    private function openTaxonBookmarks()
    {
        $this->getElement('taxonomy')->click();
    }
}
